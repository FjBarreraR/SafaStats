<?php

namespace App\Entity;

use App\Repository\RankingDinosaurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RankingDinosaurRepository::class)]
#[ORM\Table(name: 'ranking_dinosaur', schema: 'dinosaurs_project')]
class RankingDinosaur
{
    // Atributos
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'id')]
    private ?int $id = null;

    #[ORM\Column(name: 'position', nullable: false )]
    private ?int $position = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'id_dinosaur', nullable: false)]
    private ?Dinosaurs $dinosaur = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'id_ranking', nullable: false)]
    private ?Ranking $ranking = null;

    // Getter y Setter
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    public function getDinosaur(): ?Dinosaurs
    {
        return $this->dinosaur;
    }

    public function setDinosaur(?Dinosaurs $dinosaur): void
    {
        $this->dinosaur = $dinosaur;
    }

    public function getRanking(): ?Ranking
    {
        return $this->ranking;
    }

    public function setRanking(?Ranking $ranking): void
    {
        $this->ranking = $ranking;
    }

    public function __toString(): string
    {
        return sprintf(
            "RankingDinosaur [ID: %s] | Posición: %d | Dinosaurio: %s | Ranking ID: %s",
            $this->id ?? 'n/a',
            $this->position ?? 0,
            $this->dinosaur ? $this->dinosaur->getName() : 'Sin nombre',
            $this->ranking ? $this->ranking->getId() : 'Sin ranking'
        );
    }
}
