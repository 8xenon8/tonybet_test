<?php

namespace App\Service\ResultProvider;

use App\Entity\Game;

class RandomResultProvider implements IResultProvider
{
    public function setGameResult(Game $game): void
    {
        if (rand(0, 1)) {
            $game->getGameInfo()[0]->setIsWon(true);
        } else {
            $game->getGameInfo()[1]->setIsWon(true);
        }

        $game->setIsPlayed(true);
    }
}