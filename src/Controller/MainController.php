<?php

namespace App\Controller;

use App\Repository\DivisionRepository;
use App\Repository\GameRepository;
use App\Repository\TeamRepository;
use App\Service\QualificationManager;
use App\Service\PlayoffManager;
use App\Service\BestTeamProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    public function __construct(
        private DivisionRepository $divisionRepository,
        private QualificationManager $QualificationManager,
        private GameRepository $gameRepository,
        private PlayoffManager $playoffManager,
        private BestTeamProvider $bestTeamProvider,
        private TeamRepository $teamRepository
    ) {}
    /**
     * @Route("/", name="index")
     */
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $data = [];

        $this->gameRepository->clearAllGameData();

        $division1 = $this->divisionRepository->find(0);
        $division2 = $this->divisionRepository->find(1);

        $teams1 = $this->teamRepository->findByDivision($division1);
        $teams2 = $this->teamRepository->findByDivision($division2);

        $this->QualificationManager->generateQualificationData($teams1);
        $division1Data = $this->QualificationManager->getQualificationDTO();

        $this->QualificationManager->generateQualificationData($teams2);
        $division2Data = $this->QualificationManager->getQualificationDTO();

        $best1 = $this->bestTeamProvider->getBestTeams(4, $teams1);
        $best2 = $this->bestTeamProvider->getBestTeams(4, $teams2);
        $this->playoffManager->generatePlayoffData($best1, $best2);

        $playoffData = $this->playoffManager->getPlayoffDTO();

        return $this->render("base.html.twig", [
            'division1Data' => $division1Data,
            'division2Data' => $division2Data,
            'playoffData' => $playoffData
        ]);
    }
}