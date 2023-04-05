<?php

namespace App\Service;

use App\DTO\PlayoffDTO;
use Doctrine\ORM\EntityManagerInterface;

class PlayoffManager
{
    private array $totalGames = [];

    public function __construct(
        private EntityManagerInterface $entityManager,
        private GameManager $gameManager
    ) {}

    public function generatePlayoffData($teamsD1, $teamsD2): void
    {
        if (count($teamsD1) != count($teamsD2)) {
            throw new \Exception("Team count are not even");
        }

        $teamCount = count($teamsD1) + count($teamsD2);
        if ((int)log($teamCount, 2) != log($teamCount, 2)) {
            throw new \Exception("Team count is not power of 2");
        }

        $this->totalGames = [];

        $teamPairs = $this->arrangeTeamsIntoPairs($teamsD1, $teamsD2);

        while (count($teamPairs)) {
            $this->playGameStage($teamPairs);
            $teamPairs = $this->getWinnersOfLastStage();
        }

        $this->entityManager->flush();
    }

    public function getPlayoffDTO(): PlayoffDTO
    {
        return new PlayoffDTO($this->totalGames, $this->getTeamsSortedByPlace());
    }

    /** @return Team[] */
    private function getTeamsSortedByPlace(): array
    {
        $result = [];

        foreach (array_reverse($this->totalGames) as $stage) {
            foreach ($stage as $game) {
                $winner = $game->getWinner();
                if (!in_array($winner, $result)) {
                    $result[] = $winner;
                }
            }
        }

        return $result;
    }

    private function arrangeTeamsIntoPairs($teamsD1, $teamsD2): array
    {
        $teamPairs = [];

        while (count($teamsD1)) {
            $teamPairs[] = [array_shift($teamsD1), array_pop($teamsD2)];
        }
        
        return $teamPairs;
    }

    private function playGameStage(array $teamPairs): void
    {
        $games = [];

        foreach ($teamPairs as $pair) {
            $game = $this->gameManager->createPlayoffGame($pair[0], $pair[1]);
            $this->entityManager->persist($game);
            $games[] = $game;
        }

        $this->totalGames[] = $games;
    }

    private function getWinnersOfLastStage(): array
    {
        $winners = [];

        foreach (end($this->totalGames) as $game) {
            $winners[] = $game->getWinner();
        }

        if (count($winners) < 2) {
            return [];
        }

        $pairs = [];

        for ($i = 0; $i < count($winners); $i += 2) {
            $pairs[] = [$winners[$i], $winners[$i + 1]];
        }

        return $pairs;
    }
}