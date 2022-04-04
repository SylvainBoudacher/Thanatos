<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ModelRepository;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ModelRepository::class)
 */
class Model
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 1,
     *      max = 255,
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 1,
     *      max = 500,
     * )
     */
    private $description;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\PositiveOrZero
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="models")
     */
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity=Burial::class, inversedBy="models")
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    private $burial;

    /**
     * @ORM\OneToMany(targetEntity=ModelMaterial::class, mappedBy="model")
     */
    private $modelMaterials;

    /**
     * @ORM\OneToMany(targetEntity=ModelMedia::class, mappedBy="model")
     */
    private $modelMedia;

    /**
     * @ORM\OneToMany(targetEntity=ModelExtra::class, mappedBy="model")
     */
    private $modelExtras;

    public function __construct()
    {
        $this->modelMaterials = new ArrayCollection();
        $this->modelMedia = new ArrayCollection();
        $this->modelExtras = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getBurial(): ?Burial
    {
        return $this->burial;
    }

    public function setBurial(?Burial $burial): self
    {
        $this->burial = $burial;

        return $this;
    }

    /**
     * @return Collection|ModelMaterial[]
     */
    public function getModelMaterials(): Collection
    {
        return $this->modelMaterials;
    }

    public function addModelMaterial(ModelMaterial $modelMaterial): self
    {
        if (!$this->modelMaterials->contains($modelMaterial)) {
            $this->modelMaterials[] = $modelMaterial;
            $modelMaterial->setModel($this);
        }

        return $this;
    }

    public function removeModelMaterial(ModelMaterial $modelMaterial): self
    {
        if ($this->modelMaterials->removeElement($modelMaterial)) {
            // set the owning side to null (unless already changed)
            if ($modelMaterial->getModel() === $this) {
                $modelMaterial->setModel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ModelMedia[]
     */
    public function getModelMedia(): Collection
    {
        return $this->modelMedia;
    }

    public function addModelMedium(ModelMedia $modelMedium): self
    {
        if (!$this->modelMedia->contains($modelMedium)) {
            $this->modelMedia[] = $modelMedium;
            $modelMedium->setModel($this);
        }

        return $this;
    }

    public function removeModelMedium(ModelMedia $modelMedium): self
    {
        if ($this->modelMedia->removeElement($modelMedium)) {
            // set the owning side to null (unless already changed)
            if ($modelMedium->getModel() === $this) {
                $modelMedium->setModel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ModelExtra[]
     */
    public function getModelExtras(): Collection
    {
        return $this->modelExtras;
    }

    public function addModelExtra(ModelExtra $modelExtra): self
    {
        if (!$this->modelExtras->contains($modelExtra)) {
            $this->modelExtras[] = $modelExtra;
            $modelExtra->setModel($this);
        }

        return $this;
    }

    public function removeModelExtra(ModelExtra $modelExtra): self
    {
        if ($this->modelExtras->removeElement($modelExtra)) {
            // set the owning side to null (unless already changed)
            if ($modelExtra->getModel() === $this) {
                $modelExtra->setModel(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
