<?php

namespace App\DTO;

use App\Entity\Game;
use App\Entity\Team;

class QualificationDTO
{
    /** @param Game[] $games */
    public function __construct(
        private Team $team,
        /** @property Game[] */
        private array $games
    ) {}

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function getGameWithTeam(Team $team): Game|null
    {
        if ($team == $this->getTeam()) {
            return null;
        }

        foreach ($this->games as $game) {
            if (
                $game->getGameInfo()[0]->getTeam() == $team ||
                $game->getGameInfo()[1]->getTeam() == $team
            ) {
                return $game;
            }
        }

        return null;
    }
}