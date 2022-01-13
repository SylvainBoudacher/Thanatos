<?php

namespace App\Entity;

use App\Repository\MaterialRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MaterialRepository::class)
 */
class Material
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\OneToMany(targetEntity=CompanyMaterial::class, mappedBy="material")
     */
    private $companyMaterials;

    /**
     * @ORM\OneToMany(targetEntity=ModelMaterial::class, mappedBy="material")
     */
    private $modelMaterials;

    /**
     * @ORM\ManyToOne(targetEntity=Media::class, inversedBy="materials")
     */
    private $media;

    public function __construct()
    {
        $this->companyMaterials = new ArrayCollection();
        $this->modelMaterials = new ArrayCollection();
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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection|CompanyMaterial[]
     */
    public function getCompanyMaterials(): Collection
    {
        return $this->companyMaterials;
    }

    public function addCompanyMaterial(CompanyMaterial $companyMaterial): self
    {
        if (!$this->companyMaterials->contains($companyMaterial)) {
            $this->companyMaterials[] = $companyMaterial;
            $companyMaterial->setMaterial($this);
        }

        return $this;
    }

    public function removeCompanyMaterial(CompanyMaterial $companyMaterial): self
    {
        if ($this->companyMaterials->removeElement($companyMaterial)) {
            // set the owning side to null (unless already changed)
            if ($companyMaterial->getMaterial() === $this) {
                $companyMaterial->setMaterial(null);
            }
        }

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
            $modelMaterial->setMaterial($this);
        }

        return $this;
    }

    public function removeModelMaterial(ModelMaterial $modelMaterial): self
    {
        if ($this->modelMaterials->removeElement($modelMaterial)) {
            // set the owning side to null (unless already changed)
            if ($modelMaterial->getMaterial() === $this) {
                $modelMaterial->setMaterial(null);
            }
        }

        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): self
    {
        $this->media = $media;

        return $this;
    }
}
