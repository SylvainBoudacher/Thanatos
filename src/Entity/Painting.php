<?php

namespace App\Entity;

use App\Repository\PaintingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PaintingRepository::class)
 */
class Painting
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hexaCode;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\OneToMany(targetEntity=CompanyPainting::class, mappedBy="painting")
     */
    private $companyPaintings;

    public function __construct()
    {
        $this->companyPaintings = new ArrayCollection();
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

    public function getHexaCode(): ?string
    {
        return $this->hexaCode;
    }

    public function setHexaCode(?string $hexaCode): self
    {
        $this->hexaCode = $hexaCode;

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
     * @return Collection|CompanyPainting[]
     */
    public function getCompanyPaintings(): Collection
    {
        return $this->companyPaintings;
    }

    public function addCompanyPainting(CompanyPainting $companyPainting): self
    {
        if (!$this->companyPaintings->contains($companyPainting)) {
            $this->companyPaintings[] = $companyPainting;
            $companyPainting->setPainting($this);
        }

        return $this;
    }

    public function removeCompanyPainting(CompanyPainting $companyPainting): self
    {
        if ($this->companyPaintings->removeElement($companyPainting)) {
            // set the owning side to null (unless already changed)
            if ($companyPainting->getPainting() === $this) {
                $companyPainting->setPainting(null);
            }
        }

        return $this;
    }
}
