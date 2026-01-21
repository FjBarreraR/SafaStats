<?php

namespace App\Entity;

use App\Repository\RankingDinosaurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RankingDinosaurRepository::class)]
#[ORM\Table(name: 'ranking_dinosaur', schema: 'dinosaurs_project')]
class RankingDinosaur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
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


    public function getId(): ?int
    {
        return $this->id;
    }
}
