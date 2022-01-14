<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\ModelMaterialRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=ModelMaterialRepository::class)
 */
class ModelMaterial
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Model::class, inversedBy="modelMaterials")
     */
    private $model;

    /**
     * @ORM\ManyToOne(targetEntity=Material::class, inversedBy="modelMaterials")
     */
    private $material;

    /**
     * @ORM\OneToMany(targetEntity=Preparation::class, mappedBy="modelMaterial")
     */
    private $preparations;

    public function __construct()
    {
        $this->preparations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function setModel(?Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getMaterial(): ?Material
    {
        return $this->material;
    }

    public function setMaterial(?Material $material): self
    {
        $this->material = $material;

        return $this;
    }

    /**
     * @return Collection|Preparation[]
     */
    public function getPreparations(): Collection
    {
        return $this->preparations;
    }

    public function addPreparation(Preparation $preparation): self
    {
        if (!$this->preparations->contains($preparation)) {
            $this->preparations[] = $preparation;
            $preparation->setModelMaterial($this);
        }

        return $this;
    }

    public function removePreparation(Preparation $preparation): self
    {
        if ($this->preparations->removeElement($preparation)) {
            // set the owning side to null (unless already changed)
            if ($preparation->getModelMaterial() === $this) {
                $preparation->setModelMaterial(null);
            }
        }

        return $this;
    }
}
