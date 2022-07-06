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

    public const FUNERAL_DRAFT = "FUNERAL_DRAFT";
    public const FUNERAL_NEW = "FUNERAL_NEW";
    public const FUNERAL_ACCEPT = "FUNERAL_ACCEPT";
    public const FUNERAL_DRIVER_ACCEPT_TO_BRINGS_TO_FUNERAL = "FUNERAL_DRIVER_ACCEPT_TO_BRINGS_TO_FUNERAL";
    public const FUNERAL_DRIVER_BRINGS_TO_FUNERAL = "FUNERAL_DRIVER_BRINGS_TO_FUNERAL";
    public const FUNERAL_CORPSE_ARRIVES_TO_FUNERAL = "FUNERAL_CORPSE_ARRIVES_TO_FUNERAL";
    public const FUNERAL_WAITING_PROCESSING = "FUNERAL_WAITING_PROCESSING";
    public const FUNERAL_IN_PROGRESS_PROCESSING = "FUNERAL_IN_PROGRESS_PROCESSING";
    public const FUNERAL_CLOSE_PROCESSING = "FUNERAL_CLOSE_PROCESSING";
    public const FUNERAL_DRIVER_ACCEPT_BRINGS_TO_USER = "FUNERAL_DRIVER_ACCEPT_BRINGS_TO_USER";
    public const FUNERAL_DRIVER_CLOSE_BRING = "FUNERAL_DRIVER_CLOSE_BRING";

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
     * @ORM\OneToOne(targetEntity=Corpse::class, cascade={"persist", "remove"}, fetch="EAGER", inversedBy="preparation")
     * @ORM\JoinColumn(name="corpse_id", referencedColumnName="id")
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
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="preparations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $command;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=ModelMaterial::class, inversedBy="preparations")
     */
    private $modelMaterial;

    /**
     * @ORM\ManyToOne(targetEntity=ModelExtra::class)
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

    public function getCommand(): ?Order
    {
        return $this->command;
    }

    public function setCommand(?Order $command): self
    {
        $this->command = $command;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

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
