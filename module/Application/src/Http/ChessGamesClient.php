<?php

namespace Application\Http;


class ChessGamesClient
{

    protected $config;
    protected $httpClient;
    protected $cache;

    public function __construct($config, \Zend\Http\Client $httpClient, \Zend\Cache\Storage\Adapter\AbstractAdapter $cache)
    {
        $this->config = $config;
        $this->httpClient = $httpClient;
        $this->cache = $cache;
    }


    public function getPage($url) {
        $this->httpClient->setUri($url);
        $this->httpClient->setMethod('GET')->send();

        $response = $this->httpClient->getResponse();
        return $response->getBody();
    }

    function getMove($url) {

        $key = md5($url);

        $success = false;
        $moveCache = $this->cache->getItem($key, $success);

        if($success)
            return \Zend\Serializer\Serializer::unserialize($moveCache);

        $body = $this->getPage($url);
        $table = explode('<B>', substr($body, strpos($body, 'begin-moves') + 15, strpos($body, 'end-moves') - strpos($body, 'begin-moves') - 19));

        $whosMove = (strpos($body, "...") ? 'black' : 'white');

        $moves = [];

        foreach (array_slice($table, 4) as $line)
        {
            $url = substr($line, 10, strpos($line, "\">") - 10);
            if($whosMove === 'white')
                $move = preg_replace("/<.*/", "", preg_replace("/.*\.\&nbsp\;/", "", $line));
            else
                $move = preg_replace("/<.*/", "", preg_replace("/.*\.\.\./", "", $line));

            $gamecount = str_replace(',','',preg_replace("/<.*/", "", preg_replace("/.*&nbsp;/", "",$line)));

            $whiteOdds = 0;
            $blackOdds = 0;
            $drawcount = 0;

            $blacknext = false;
            $whitenext = false;
            $drawnext = false;

            $lines = explode(">", substr($line, strpos($line, "table")));

            foreach ($lines as $oddline)
            {
                if (strpos($url, "chessgame?gid=") !== false) {
                    if (strpos($line, "1-0") != false)
                        $whiteOdds = 1;
                    else if (strpos($line, "0-1") != false)
                        $blackOdds = 1;
                    else if (strpos($line, "1/2-1/2") != false)
                        $drawcount = 1;
                    else
                        throw new \InvalidArgumentException("No disposition found for game win");
                }

                if(strpos($oddline, "bgcolor=#000000") != false)
                    $blacknext = true;
                else if(strpos($oddline, "bgcolor=#CCCCCC") != false)
                    $drawnext = true;
                else if(strpos($oddline, "bgcolor=#FFFFFF") != false)
                    $whitenext = true;

                if(strpos($oddline, "%") !== false) {
                    if ($blacknext) {
                        $blacknext = false;
                        $blackOdds = substr($oddline, 0, strpos($oddline, '%'));
                    }
                    else if ($whitenext)
                    {
                        $whitenext = false;
                        $whiteOdds = substr($oddline, 0, strpos($oddline, '%'));
                    }
                    else if ($drawnext)
                    {
                        $drawnext = false;
                        $drawcount = substr($oddline, 0, strpos($oddline, '%'));
                    }
                    else
                        throw new \InvalidArgumentException("Percentage with no designation");
                }
            }

            $moves[] = [
                'move' => $move,
                'gameCount' => $gamecount,
                'blackOdds' => $blackOdds,
                'whiteOdds' => $whiteOdds,
                'drawOdds' => $drawcount,
                'url' => substr($url, 14),
            ];

        }

        $result = [
            'moves' => $moves,
            'cached' => true
        ];

        $this->cache->setItem($key, \Zend\Serializer\Serializer::serialize($result));

        $result['cached'] = false;

        return $result;
    }
}