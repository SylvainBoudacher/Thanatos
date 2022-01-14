<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\PreparationRepository;

/**
 * @ORM\Entity(repositoryClass=PreparationRepository::class)
 */
class Preparation
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\OneToOne(targetEntity=Corpse::class, cascade={"persist", "remove"})
     */
    private $corpse;

    /**
     * @ORM\ManyToOne(targetEntity=Theme::class, inversedBy="preparations")
     */
    private $theme;

    /**
     * @ORM\ManyToOne(targetEntity=Painting::class, inversedBy="preparations")
     */
    private $painting;

    /**
     * @ORM\ManyToOne(targetEntity=ModelMaterial::class, inversedBy="preparations")
     */
    private $modelMaterial;

    /**
     * @ORM\ManyToOne(targetEntity=ModelExtra::class, inversedBy="preparations")
     */
    private $modelExtra;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCorpse(): ?Corpse
    {
        return $this->corpse;
    }

    public function setCorpse(?Corpse $corpse): self
    {
        $this->corpse = $corpse;

        return $this;
    }

    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    public function setTheme(?Theme $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function getPainting(): ?Painting
    {
        return $this->painting;
    }

    public function setPainting(?Painting $painting): self
    {
        $this->painting = $painting;

        return $this;
    }

    public function getModelMaterial(): ?ModelMaterial
    {
        return $this->modelMaterial;
    }

    public function setModelMaterial(?ModelMaterial $modelMaterial): self
    {
        $this->modelMaterial = $modelMaterial;

        return $this;
    }

    public function getModelExtra(): ?ModelExtra
    {
        return $this->modelExtra;
    }

    public function setModelExtra(?ModelExtra $modelExtra): self
    {
        $this->modelExtra = $modelExtra;

        return $this;
    }
}
