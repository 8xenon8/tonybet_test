<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 63)]
    private ?string $name = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Division $division = null;

    #[ORM\OneToMany(mappedBy: 'team', targetEntity: GameInfo::class, orphanRemoval: true)]
    private Collection $gamesInfo;

    public function __construct()
    {
        $this->gamesInfo = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDivision(): ?Division
    {
        return $this->division;
    }

    public function setDivision(?Division $division): self
    {
        $this->division = $division;

        return $this;
    }

    /**
     * @return Collection<int, GameInfo>
     */
    public function getGamesInfo(): Collection
    {
        return $this->gamesInfo;
    }

    public function addGamesInfo(GameInfo $gamesInfo): self
    {
        if (!$this->gamesInfo->contains($gamesInfo)) {
            $this->gamesInfo->add($gamesInfo);
            $gamesInfo->setTeam($this);
        }

        return $this;
    }

    public function removeGamesInfo(GameInfo $gamesInfo): self
    {
        if ($this->gamesInfo->removeElement($gamesInfo)) {
            // set the owning side to null (unless already changed)
            if ($gamesInfo->getTeam() === $this) {
                $gamesInfo->setTeam(null);
            }
        }

        return $this;
    }

    public function getWonQualificationGamesCount(): int
    {
        $result = 0;

        foreach ($this->gamesInfo as $gameInfo) {
            if ($gameInfo->isIsWon() && $gameInfo->getGame()->isQualificationGame() && $gameInfo->isIsHome()) {
                $result++;
            }
        }

        return $result;
    }
}
