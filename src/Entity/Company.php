<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CompanyRepository::class)
 */
class Company
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
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $siret;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $iban;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rib;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bic;

    /**
     * @ORM\ManyToOne(targetEntity=Media::class, inversedBy="companies")
     */
    private $media;

    /**
     * @ORM\ManyToOne(targetEntity=Address::class, inversedBy="companies")
     */
    private $address;

    /**
     * @ORM\OneToMany(targetEntity=CompanyMaterial::class, mappedBy="company")
     */
    private $companyMaterials;

    /**
     * @ORM\OneToMany(targetEntity=CompanyExtra::class, mappedBy="company")
     */
    private $companyExtras;

    /**
     * @ORM\OneToMany(targetEntity=CompanyPainting::class, mappedBy="company")
     */
    private $companyPaintings;

    public function __construct()
    {
        $this->companyMaterials = new ArrayCollection();
        $this->companyExtras = new ArrayCollection();
        $this->companyPaintings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
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

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function getIban(): ?string
    {
        return $this->iban;
    }

    public function setIban(?string $iban): self
    {
        $this->iban = $iban;

        return $this;
    }

    public function getRib(): ?string
    {
        return $this->rib;
    }

    public function setRib(?string $rib): self
    {
        $this->rib = $rib;

        return $this;
    }

    public function getBic(): ?string
    {
        return $this->bic;
    }

    public function setBic(?string $bic): self
    {
        $this->bic = $bic;

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

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

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
            $companyMaterial->setCompany($this);
        }

        return $this;
    }

    public function removeCompanyMaterial(CompanyMaterial $companyMaterial): self
    {
        if ($this->companyMaterials->removeElement($companyMaterial)) {
            // set the owning side to null (unless already changed)
            if ($companyMaterial->getCompany() === $this) {
                $companyMaterial->setCompany(null);
            }
        }

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
            $companyExtra->setCompany($this);
        }

        return $this;
    }

    public function removeCompanyExtra(CompanyExtra $companyExtra): self
    {
        if ($this->companyExtras->removeElement($companyExtra)) {
            // set the owning side to null (unless already changed)
            if ($companyExtra->getCompany() === $this) {
                $companyExtra->setCompany(null);
            }
        }

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
            $companyPainting->setCompany($this);
        }

        return $this;
    }

    public function removeCompanyPainting(CompanyPainting $companyPainting): self
    {
        if ($this->companyPaintings->removeElement($companyPainting)) {
            // set the owning side to null (unless already changed)
            if ($companyPainting->getCompany() === $this) {
                $companyPainting->setCompany(null);
            }
        }

        return $this;
    }
}
