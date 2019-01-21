<?php

namespace Application\Service\Factory;
use Interop\Container\ContainerInterface;
use MongoDB\Driver\Exception\AuthenticationException;
use Zend\Http\Client\Adapter\Curl;
use Zend\ServiceManager\Factory\FactoryInterface;

class ChessGamesClient
{
    static protected $chessClient;

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $configurationService = $container->get('Configuration');
        $config = $configurationService['chessGames'];
        $params = $config['params'];

        $httpClient = new \Zend\Http\Client();

        if(!file_exists($params['loginCookieLocation']))
        {
            $this->login($httpClient, $params);
        }
        else{
            /** @var \Zend\Http\Cookies $rawCookie */
            $rawCookie = \Zend\Serializer\Serializer::unserialize(file_get_contents($params['loginCookieLocation']));

            $httpClient->addCookie($rawCookie);
        }

        /** @var \Zend\Cache\Storage\Adapter\AbstractAdapter $cache */
        $cache = $config['cacheAdapter'];

        $chessClient = new \Application\Http\ChessGamesClient($config, $httpClient, $cache);
        
        
        return $chessClient;
    }


    /**
     * @param $httpClient
     */
    public function login(\Zend\Http\Client $httpClient, $params)
    {


        $httpClient->setUri($params['loginUrl']);
        $httpClient->setParameterPost([
            'name' => $params['username'],
            'password' => $params['password']
        ]);

        $response = $httpClient->setMethod('POST')->send();

        $cookiejar = \Zend\Http\Cookies::fromResponse($response);

        if($cookiejar->getAllCookies()[0]->getName()  != "chessID")
            throw new AuthenticationException('Could not login to chessgames');

        file_put_contents($params['loginCookieLocation'], \Zend\Serializer\Serializer::serialize($cookiejar->getAllCookies()[0]));

        $httpClient->resetParameters();
        $httpClient->addCookie($cookiejar->getAllCookies()[0]);
    }
}