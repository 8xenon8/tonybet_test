<?php

namespace App\DTO;

use App\Entity\Game;

class PlayoffDTO
{
    public function __construct(
        private array $games,
        private array $winners
    ) {
        $this->games = array_reverse($games);
    }

    public function getFinalGame(): Game|null
    {
        if (!count($this->games)) {
            return null;
        }

        return $this->games[0][0];
    }

    public function getSemifinalGames(): array|null
    {
        if (!isset($this->games[1])) {
            return null;
        }

        return $this->games[1];
    }

    public function getQuarterfinalGames(): array|null
    {
        if (!isset($this->games[2])) {
            return null;
        }

        return $this->games[2];
    }

    public function getWinners(): array
    {
        return $this->winners;
    }
}