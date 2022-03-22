<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\MaterialRepository;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MaterialRepository::class)
 */
class Material
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
     *      max = 120,
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\PositiveOrZero
     *
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
     * @ORM\ManyToOne(targetEntity=Media::class, inversedBy="materials", cascade={"persist"})
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
