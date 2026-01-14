<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
#[ORM\Table(name: 'review', schema: 'dinosaursProject')]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id')]
    private ?int $id = null;

    #[ORM\Column(name: 'comment', length: 10000, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(name: 'rating', nullable: true)]
    private ?int $rating = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name:'id_dinosaur' , nullable: true)]
    private ?Dinosaurs $dinosaur = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name:'id_user' , nullable: true)]
    private ?User $user = null;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }
    public function getDinosaur(): ?Dinosaurs
    {
        return $this->dinosaur;
    }

    public function setDinosaur(?Dinosaurs $dinosaur): static
    {
        $this->dinosaur = $dinosaur;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
