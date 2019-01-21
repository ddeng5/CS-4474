<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ChessGamesController extends AbstractActionController
{
    protected $chessGamesService;

    function __construct(\Application\Service\ChessGames $chessGamesService)
    {
        $this->chessGamesService = $chessGamesService;
    }

    public function exampleAction()
    {
        return new ViewModel();
    }

    public function getMoveAction()
    {
        $url = $this->params('url');

        return new JsonModel(['moves' => $this->chessGamesService->getMove("http://www.chessgames.com/perl/explorer?".$url)]);
    }

    public function indexAction()
    {
        $moves = $this->chessGamesService->getOpeningMoves();
        return new ViewModel(['moves' => $moves]);
    }
}
