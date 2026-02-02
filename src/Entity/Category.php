<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\InverseJoinColumn;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'category', schema: 'dinosaurs_project')]

class Category
{
    // Atributos
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;

    #[ORM\Column(length: 800)]
    private ?string $image = null;

    #[ORM\ManyToMany(targetEntity: Dinosaurs::class)]
    #[JoinTable(name: 'dinosaurs_project.category_dinosaur')]
    #[JoinColumn(name: 'id_category', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'id_dinosaur', referencedColumnName: 'id')]
    private ?Collection $dinosaurs;

    // Constructor
    public function __construct()
    {
        $this->dinosaurs = new ArrayCollection();
    }

    // Getter y Setter
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getDinosaurs(): ?Collection
    {
        return $this->dinosaurs;
    }

    public function setDinosaurs(?Collection $dinosaurs): void
    {
        $this->dinosaurs = $dinosaurs;
    }

    public function addDinosaur(Dinosaurs $dinosaur): static {
        if (!$this->dinosaurs->contains($dinosaur)) {
            $this->dinosaurs->add($dinosaur);
        }

        return $this;
    }

    public function removeDinosaur(Dinosaurs $dinosaur): static
    {
        // Esta es la parte que te falta:
        $this->dinosaurs->removeElement($dinosaur);

        return $this;
    }
}
