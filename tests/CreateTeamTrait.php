<?php

namespace Test;

use App\Entity\Team;

trait CreateTeamTrait
{
    private function createTeam(string $name, int $winsNum): Team
    {
        static $id = 0;

        $teamMock = $this->createMock(Team::class);
        $teamMock->expects(self::any())
            ->method('getId')
            ->willReturn($id++);
        $teamMock->expects(self::any())
            ->method('getName')
            ->willReturn($name);
        $teamMock->expects(self::any())
            ->method('getWonQualificationGamesCount')
            ->willReturn($winsNum);

        return $teamMock;
    }
}