<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    public const TYPE_QUALIFICATION = 0;
    public const TYPE_PLAYOFF = 1;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private int $type;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: GameInfo::class, cascade: ["all"], orphanRemoval: true)]
    private Collection $gameInfo;

    #[ORM\Column]
    private ?bool $isPlayed = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'preceedingGames', cascade: ["all"])]
    private ?self $succeedingGame = null;

    #[ORM\OneToMany(mappedBy: 'succeedingGame', targetEntity: self::class, cascade: ["all"])]
    private Collection $preceedingGames;

    public function __construct()
    {
        $this->gameInfo = new ArrayCollection();
        $this->preceedingGames = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function isQualificationGame(): bool
    {
        return $this->getType() === self::TYPE_QUALIFICATION;
    }

    /**
     * @return Collection<int, GameInfo>
     */
    public function getGameInfo(): Collection
    {
        return $this->gameInfo;
    }

    public function addGameInfo(GameInfo $gameInfo): self
    {
        if (!$this->gameInfo->contains($gameInfo)) {
            $this->gameInfo->add($gameInfo);
            $gameInfo->setGame($this);
        }

        return $this;
    }

    public function removeGameInfo(GameInfo $gameInfo): self
    {
        if ($this->gameInfo->removeElement($gameInfo)) {
            // set the owning side to null (unless already changed)
            if ($gameInfo->getGame() === $this) {
                $gameInfo->setGame(null);
            }
        }

        return $this;
    }

    public function isIsPlayed(): ?bool
    {
        return $this->isPlayed;
    }

    public function setIsPlayed(bool $isPlayed): self
    {
        $this->isPlayed = $isPlayed;

        return $this;
    }

    public function getSucceedingGame(): ?self
    {
        return $this->succeedingGame;
    }

    public function setSucceedingGame(?self $succeedingGame): self
    {
        $this->succeedingGame = $succeedingGame;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getPreceedingGames(): Collection
    {
        return $this->preceedingGames;
    }

    public function addPreceedingGame(self $preceedingGame): self
    {
        if (!$this->preceedingGames->contains($preceedingGame)) {
            $this->preceedingGames->add($preceedingGame);
            $preceedingGame->setSucceedingGame($this);
        }

        return $this;
    }

    public function removePreceedingGame(self $preceedingGame): self
    {
        if ($this->preceedingGames->removeElement($preceedingGame)) {
            // set the owning side to null (unless already changed)
            if ($preceedingGame->getSucceedingGame() === $this) {
                $preceedingGame->setSucceedingGame(null);
            }
        }

        return $this;
    }

    public function getWinner(): Team
    {
        if (!$this->isIsPlayed()) {
            throw new \Exception("Game is not played yet");
        }

        if ($this->getGameInfo()[0]->isIsWon()) {
            return $this->getGameInfo()[0]->getTeam();
        } else {
            return $this->getGameInfo()[1]->getTeam();
        }
    }

    public function setQualificationType(): void
    {
        $this->setType(self::TYPE_QUALIFICATION);
    }

    public function setPlayoffType(): void
    {
        $this->setType(self::TYPE_PLAYOFF);
    }
}
