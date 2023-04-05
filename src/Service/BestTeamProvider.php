<?php

namespace App\Service;

use App\Service\TeamDraw\ITeamDrawManager;

class BestTeamProvider
{
    public function __construct(
        private ITeamDrawManager $teamDrawManager
    ) {}

    /**
     * @return Team[]
     */
    public function getBestTeams(int $numberOfTeams, array $teamsToSelect): array
    {
        if (!count($teamsToSelect) || 0 == $numberOfTeams) {
            return [];
        }

        $result = [];
        $scores = [];

        usort($teamsToSelect, function($a, $b) {
            return $b->getWonQualificationGamesCount() - $a->getWonQualificationGamesCount();
        });

        foreach ($teamsToSelect as $team) {
            $scores[$team->getId()] = $team->getWonQualificationGamesCount();
        }

        arsort($scores);

        $lastCount = 0;
        $i = 0;
        foreach ($scores as $key => $value) {
            if ($i >= $numberOfTeams) { break; }

            foreach ($teamsToSelect as $team) {
                if ($team->getId() === $key) {
                    $result[] = $team;
                }
            }

            $lastCount = $value;
            $i++;
        }

        $lastCountOccurences = array_keys(array_values($scores), $lastCount);
        $firstOccurence = $lastCountOccurences[0];
        $lastOccurence = end($lastCountOccurences);

        $numberOfTeamsToDraw = $lastOccurence + 1 - $firstOccurence;
        $teamsToDraw = array_slice($teamsToSelect, $firstOccurence, $numberOfTeamsToDraw);

        $drawedTeams = $this->teamDrawManager->drawTeams($teamsToDraw, $numberOfTeams - $firstOccurence);

        array_splice($result, $firstOccurence, $numberOfTeams - $firstOccurence, $drawedTeams);

        return $result;
    }
}