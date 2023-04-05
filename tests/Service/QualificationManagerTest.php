<?php

use App\Entity\Game;
use App\Entity\GameInfo;
use App\Entity\Team;
use App\Service\GameManager;
use App\Service\QualificationManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Test\CreateTeamTrait;

class QualificationManagerTest extends KernelTestCase
{
    use CreateTeamTrait;

    /** @dataProvider dataProvider */
    public function test($teams, $expectedWins)
    {
        $service = $this->getService();

        $service->generateQualificationData($teams);

        $relfection = new ReflectionObject($service);
        $qualificationData = $relfection->getProperty('qualificationData')->getValue($service);

        $this->assertCount(count($teams), $qualificationData);

        $teamCount = count($teams);

        foreach ($qualificationData as $i => $teamData) {
            $wins = 0;
            $this->assertCount($teamCount - 1, $teamData['games']);

            foreach ($teamData['games'] as $game) {
                $team1 = $game->getGameInfo()[0]->getTeam();
                $team2 = $game->getGameInfo()[1]->getTeam();

                $this->assertNotEquals(
                    $team1->getName(),
                    $team2->getName()
                );

                $this->assertContains($teamData['team'], [$team1, $team2]);

                if ($game->getWinner()->getName() == $teamData['team']->getName()) {
                    $wins++;
                }
            }

            $this->assertEquals($expectedWins[$i], $wins);
        }
    }

    private function getService(): QualificationManager
    {
        $emMock = $this->createMock(EntityManagerInterface::class);

        $gameManagerMock = $this->createMock(GameManager::class);
        $gameManagerMock->expects(self::any())
            ->method('createQualificationGame')
            ->will(
                $this->returnCallback(
                    function(Team $homeTeam, Team $awayTeam) {
                        $game = new Game();

                        $gameInfoHome = new GameInfo();
                        $gameInfoHome->setTeam($homeTeam)
                            ->setGame($game)
                            ->setIsHome(true)
                            ->setIsWon(true);

                        $gameInfoAway = new GameInfo();
                        $gameInfoAway->setTeam($awayTeam)
                            ->setGame($game)
                            ->setIsHome(false)
                            ->setIsWon(false);

                        $game->addGameInfo($gameInfoHome)
                            ->addGameInfo($gameInfoAway)
                            ->setIsPlayed(true);

                        return $game;
                    }
                )
            );

        return new QualificationManager($gameManagerMock, $emMock);
    }

    private function dataProvider()
    {
        return [
            [
                [], []
            ],
            [
                [
                    $this->createTeam('a', 0)
                ],
                [0]
            ],
            [
                [
                    $this->createTeam('a', 0),
                    $this->createTeam('b', 0)
                ],
                [1,1]
            ],
            [
                [
                    $this->createTeam('a', 0),
                    $this->createTeam('b', 0),
                    $this->createTeam('c', 0),
                ],
                [2,2,2]
            ],
            [
                [
                    $this->createTeam('a', 0),
                    $this->createTeam('b', 0),
                    $this->createTeam('c', 0),
                    $this->createTeam('d', 0)
                ],
                [3,3,3,3]
            ]
        ];
    }
}