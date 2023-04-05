<?php

use App\Entity\Game;
use App\Entity\GameInfo;
use App\Entity\Team;
use App\Service\GameManager;
use App\Service\PlayoffManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Test\CreateTeamTrait;

class PlayoffManagerTest extends KernelTestCase
{
    use CreateTeamTrait;

    /** @dataProvider dataProvider */
    public function test($teams1, $teams2, $expectedWinners)
    {
        $service = $this->getService();

        $service->generatePlayoffData($teams1, $teams2);

        $relfection = new ReflectionObject($service);
        $games = $relfection->getProperty('totalGames')->getValue($service);

        foreach (array_reverse($games) as $i => $stage) {
            $this->assertCount(pow(2, $i), $stage);

            foreach ($stage as $j => $game) {
                $this->assertEquals($expectedWinners[$i][$j], $game->getWinner()->getName());
            }
        }
    }

    /** @dataProvider incorrectDataProvider */
    public function testServiceThrowsException($teams1, $teams2, $message)
    {
        $service = $this->getService();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($message);
        
        $service->generatePlayoffData($teams1, $teams2);
    }

    private function getService(): PlayoffManager
    {
        $emMock = $this->createMock(EntityManagerInterface::class);
        
        $gameManagerMock = $this->createMock(GameManager::class);
        $gameManagerMock->expects(self::any())
            ->method('createPlayoffGame')
            ->will(
                $this->returnCallback(
                    function(Team $homeTeam, Team $awayTeam) {
                        $game = new Game();

                        $isHomeWinner = strcmp($homeTeam->getName(), $awayTeam->getName()) < 0;

                        $gameInfoHome = new GameInfo();
                        $gameInfoHome->setTeam($homeTeam)
                            ->setGame($game)
                            ->setIsHome(true)
                            ->setIsWon($isHomeWinner);

                        $gameInfoAway = new GameInfo();
                        $gameInfoAway->setTeam($awayTeam)
                            ->setGame($game)
                            ->setIsHome(false)
                            ->setIsWon(!$isHomeWinner);

                        $game->addGameInfo($gameInfoHome)
                            ->addGameInfo($gameInfoAway)
                            ->setIsPlayed(true);

                        return $game;
                    }
                )
            );

        return new PlayoffManager($emMock, $gameManagerMock);
    }

    private function dataProvider()
    {
        return [
            [
                [
                    $this->createTeam('a', 1),
                    $this->createTeam('b', 1),
                ],
                [
                    $this->createTeam('c', 1),
                    $this->createTeam('d', 1),
                ],
                [
                    ['a'],
                    ['a', 'b'],
                ]
            ],
            [
                [
                    $this->createTeam('a', 1),
                    $this->createTeam('b', 2),
                    $this->createTeam('c', 3),
                    $this->createTeam('d', 4),
                ],
                [
                    $this->createTeam('e', 4),
                    $this->createTeam('f', 3),
                    $this->createTeam('g', 2),
                    $this->createTeam('h', 1),
                ],
                [
                    ['a'],
                    ['a', 'c'],
                    ['a', 'b', 'c', 'd']
                ]
            ],
            [
                [
                    $this->createTeam('a', 1),
                    $this->createTeam('b', 2),
                    $this->createTeam('g', 3),
                    $this->createTeam('h', 4),
                ],
                [
                    $this->createTeam('e', 1),
                    $this->createTeam('f', 2),
                    $this->createTeam('c', 3),
                    $this->createTeam('d', 4),
                ],
                [
                    ['a'],
                    ['a', 'e'],
                    ['a', 'b', 'f', 'e']
                ]
            ]
        ];
    }

    private function incorrectDataProvider()
    {
        return [
            [
                [1, 2, 3],
                [1, 2],
                'Team count are not even',
            ],
            [
                [1, 2, 3],
                [1, 2, 3],
                'Team count is not power of 2'
            ]
        ];
    }
}