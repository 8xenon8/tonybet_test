<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\GameInfo;
use App\Entity\Team;
use App\Service\ResultProvider\IResultProvider;

class GameManager
{
    public function __construct(
        private IResultProvider $resultProvier
    ) {} 

    public function createPlayoffGame(Team $homeTeam, Team $awayTeam): Game
    {
        $game = $this->createGame($homeTeam, $awayTeam);
        $game->setPlayoffType();
        return $game;
    }

    public function createQualificationGame(Team $homeTeam, Team $awayTeam): Game
    {
        $game = $this->createGame($homeTeam, $awayTeam);
        $game->setQualificationType();
        return $game;
    }

    private function createGame(Team $homeTeam, Team $awayTeam): Game
    {
        $game = new Game();
        $game->setIsPlayed(false);

        $game->addGameInfo($this->createGameInfo($game, $homeTeam, true));
        $game->addGameInfo($this->createGameInfo($game, $awayTeam, false));
        
        $this->resultProvier->setGameResult($game);

        return $game;
    }

    private function createGameInfo(Game $game, Team $team, bool $isHome): GameInfo
    {
        $gameInfo = new GameInfo();
        $gameInfo->setTeam($team)
            ->setGame($game)
            ->setIsWon(false)
            ->setIsHome($isHome);
        return $gameInfo;
    }
}