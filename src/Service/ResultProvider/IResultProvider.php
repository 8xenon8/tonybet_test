<?php

namespace App\Service\ResultProvider;

use App\Entity\Game;

interface IResultProvider
{
    public function setGameResult(Game $game): void;
}