<?php

namespace App\Entity;

use App\Repository\DinosaursRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DinosaursRepository::class)]
#[ORM\Table(name: 'dinosaur', schema: 'dinosaurs_project')]
class Dinosaurs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id')]
    private ?int $id = null;

    #[ORM\Column(name:"code", length: 100 )]
    private ?string $code = null;

    #[ORM\Column( name: 'name', length: 150, nullable: false)]
    private ?string $name = null;

    #[ORM\Column(name: 'weight', length: 100, nullable: false)]
    private ?string $weight = null;

    #[ORM\Column(name: 'height', length: 100, nullable: false)]
    private ?string $height = null;

    #[ORM\Column(name: 'length', length: 100, nullable: true)]
    private ?string $length = null;

    #[ORM\Column(name: 'diet', length: 255, nullable: false)]
    private ?string $diet = null;

    #[ORM\Column(name: 'period', length: 255, nullable: false)]
    private ?string $period = null;

    #[ORM\Column(name: 'existed', length: 255 ,nullable: false)]
    private ?string $existed = null;

    #[ORM\Column(name: 'region', length: 200, nullable: false)]
    private ?string $region = null;

    #[ORM\Column(name: 'type', length: 255, nullable: false)]
    private ?string $type = null;

    #[ORM\Column(name: 'description', length: 10000, nullable: false)]
    private ?string $description = null;

    #[ORM\Column(name: 'image', length: 800, nullable: false)]
    private ?string $image = null;

    #[ORM\Column(name: 'ispopular', nullable: false)]
    private ?bool $isPopular = null;

    #[ORM\ManyToMany(targetEntity: Category::class, mappedBy: 'dinosaurs')]
    private ?Collection $categories;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(?string $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getHeight(): ?string
    {
        return $this->height;
    }

    public function setHeight(?string $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function getLength(): ?string
    {
        return $this->length;
    }

    public function setLength(?string $length): static
    {
        $this->length = $length;

        return $this;
    }

    public function getDiet(): ?string
    {
        return $this->diet;
    }

    public function setDiet(?string $diet): static
    {
        $this->diet = $diet;

        return $this;
    }

    public function getPeriod(): ?string
    {
        return $this->period;
    }

    public function setPeriod(?string $period): static
    {
        $this->period = $period;

        return $this;
    }

    public function getExisted(): ?string
    {
        return $this->existed;
    }

    public function setExisted(?string $existed): static
    {
        $this->existed = $existed;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function isPopular(): ?bool
    {
        return $this->isPopular;
    }

    public function setIsPopular(?bool $isPopular): static
    {
        $this->isPopular = $isPopular;

        return $this;
    }
}
