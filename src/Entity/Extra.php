<?php

namespace App\Entity;

use App\Repository\ExtraRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ExtraRepository::class)
 */
class Extra
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\OneToMany(targetEntity=CompanyExtra::class, mappedBy="extra")
     */
    private $companyExtras;

    public function __construct()
    {
        $this->companyExtras = new ArrayCollection();
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

    /**
     * @return Collection|CompanyExtra[]
     */
    public function getCompanyExtras(): Collection
    {
        return $this->companyExtras;
    }

    public function addCompanyExtra(CompanyExtra $companyExtra): self
    {
        if (!$this->companyExtras->contains($companyExtra)) {
            $this->companyExtras[] = $companyExtra;
            $companyExtra->setExtra($this);
        }

        return $this;
    }

    public function removeCompanyExtra(CompanyExtra $companyExtra): self
    {
        if ($this->companyExtras->removeElement($companyExtra)) {
            // set the owning side to null (unless already changed)
            if ($companyExtra->getExtra() === $this) {
                $companyExtra->setExtra(null);
            }
        }

        return $this;
    }
}
