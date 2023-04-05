<?php

namespace App\Service\TeamDraw;

class RandomTeamDrawManager implements ITeamDrawManager
{
    public function drawTeams(array $teams, int $numOfTeamsToSelect): array
    {
        while (count($teams) > $numOfTeamsToSelect) {
            array_splice($teams, rand(0, count($teams)), 1);
        }

        return $teams;
    }
}