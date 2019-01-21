<?php

namespace Application\Service;


use Application\Service\Factory\ChessGamesClient;
use Doctrine\ORM\Mapping\Entity;
use Zend\Mvc\Application;


class ChessGames
{
    /** @var \Doctrine\ORM\EntityManager */
    protected $entityManager;
    protected $chessGamesClient;

    function __construct(\Doctrine\ORM\EntityManager $entityManager, \Application\Http\ChessGamesClient $chessGamesClient)
    {
        $this->entityManager = $entityManager;
        $this->chessGamesClient = $chessGamesClient;
    }

    function getOpeningMoves() {
        return $this->getMove('http://www.chessgames.com/perl/explorer');

    }

    function getMove($url) {
        return $this->chessGamesClient->getMove($url);
    }
}