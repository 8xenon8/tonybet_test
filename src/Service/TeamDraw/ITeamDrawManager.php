<?php

namespace App\Service\TeamDraw;

interface ITeamDrawManager
{
    /**
     * @param Team[] $teams
     * @return Team[]
     */
    public function drawTeams(array $teams, int $numOfTeamsToSelect): array;
}