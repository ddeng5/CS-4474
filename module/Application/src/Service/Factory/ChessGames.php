<?php

namespace Application\Service\Factory;
use Interop\Container\ContainerInterface;
use Zend\Http\Client\Adapter\Curl;
use Zend\ServiceManager\Factory\FactoryInterface;

class ChessGames
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $chessGamesClient = $container->get('ChessGamesClient');
        $opening = new \Application\Service\ChessGames($entityManager, $chessGamesClient);


        return $opening;
    }
}