<?php

namespace App\Service;

use App\DTO\QualificationDTO;
use Doctrine\ORM\EntityManagerInterface;

class QualificationManager
{
    private $qualificationData = [];

    public function __construct(
        private GameManager $gameManager,
        private EntityManagerInterface $entityManager
    ) {}

    public function generateQualificationData(array $teams): void
    {
        $this->qualificationData = [];

        foreach ($teams as $team) {
            $teamGames = [];

            foreach ($teams as $team2) {
                if ($team == $team2) {
                    continue;
                }

                $game = $this->gameManager->createQualificationGame($team, $team2);
                $this->entityManager->persist($game);

                $teamGames[] = $game;
            }

            $this->qualificationData[] = [
                'team' => $team,
                'games' => $teamGames
            ];
        }

        $this->entityManager->flush();
    }

    /** @return QualificationDTO[] */
    public function getQualificationDTO(): array
    {
        $DTOs = [];

        foreach ($this->qualificationData as $teamData) {
            $DTOs[] = new QualificationDTO($teamData['team'], $teamData['games']);
        }

        return $DTOs;
    }
}