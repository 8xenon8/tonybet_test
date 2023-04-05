<?php

namespace Test\Service;

use App\Entity\Team;
use App\Service\BestTeamProvider;
use App\Service\TeamDraw\ITeamDrawManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Test\CreateTeamTrait;

class BestTeamProviderTest extends KernelTestCase
{
    use CreateTeamTrait;

    /** @dataProvider dataProvider */
    public function test(array $teams, int $teamsToSelect, array $expectedResult)
    {
        $service = $this->getService();

        $selectedTeams = $service->getBestTeams($teamsToSelect, $teams);

        $this->assertCount(count($expectedResult), $selectedTeams);

        for ($i = 0; $i < count($expectedResult); $i++) {
            $this->assertEquals($expectedResult[$i], $selectedTeams[$i]->getWonQualificationGamesCount());
        }
    }

    private function getService(): BestTeamProvider
    {
        $teamDrawManagerMock = $this->createMock(ITeamDrawManager::class);
        $teamDrawManagerMock->expects(self::any())
            ->method('drawTeams')
            ->will(
                $this->returnCallback(
                    function(array $teams, int $numOfTeamsToSelect): array {
                        return array_slice($teams, 0, $numOfTeamsToSelect);
                    }
                )
            );

        return new BestTeamProvider($teamDrawManagerMock);
    }

    public function dataProvider(): array
    {
        return [
            [
                [
                    $this->createTeam('a', 1),
                    $this->createTeam('b', 2),
                    $this->createTeam('c', 3),
                    $this->createTeam('d', 4),
                    $this->createTeam('e', 5),
                    $this->createTeam('f', 6)
                ],
                6,
                [6, 5, 4, 3, 2, 1]
            ],
            [
                [
                    $this->createTeam('a', 1),
                    $this->createTeam('b', 2),
                    $this->createTeam('c', 3),
                    $this->createTeam('d', 4),
                    $this->createTeam('e', 5),
                    $this->createTeam('f', 6)
                ],
                3,
                [6, 5, 4]
            ],
            [
                [
                    $this->createTeam('a', 1),
                    $this->createTeam('b', 2),
                    $this->createTeam('c', 3),
                    $this->createTeam('d', 4),
                    $this->createTeam('e', 5),
                    $this->createTeam('f', 6)
                ],
                7,
                [6, 5, 4, 3, 2, 1]
            ],
            [
                [
                    $this->createTeam('a', 1),
                    $this->createTeam('b', 1),
                    $this->createTeam('c', 2),
                    $this->createTeam('d', 2)
                ],
                3,
                [2, 2, 1]
            ],
            [
                [
                    $this->createTeam('a', 1),
                    $this->createTeam('b', 1),
                    $this->createTeam('c', 1)
                ],
                3,
                [1, 1, 1]
            ],
            [
                [
                    $this->createTeam('a', 0),
                    $this->createTeam('b', 0),
                    $this->createTeam('c', 1)
                ],
                3,
                [1, 0, 0]
            ],
            [
                [],
                3,
                []
            ]
        ];
    }
}