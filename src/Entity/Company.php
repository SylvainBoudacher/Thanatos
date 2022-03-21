<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CompanyRepository;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CompanyRepository::class)
 */
class Company
{
    use TimestampableTrait;
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    //TODO: est ce que "type" sert vraiment a qqchose quand on a la colonne "roles" aussi
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Regex("/^[a-z-]+$/i")
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Regex("/^[0-9]{14}$/")
     */
    private $siret;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Regex("/^([A-Z]{2}[ \-]?[0-9]{2})(?=(?:[ \-]?[A-Z0-9]){9,30}$)((?:[ \-]?[A-Z0-9]{3,5}){2,7})([ \-]?[A-Z0-9]{1,3})?$/")
     */
    private $iban;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotNull
     * @Assert\Regex("/(?<B>\d{5})(?<G>\d{5})(?<C>\w{11})(?<K>\d{2})/")
     * @Assert\NotBlank
     */
    private $rib;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Regex("/^[a-z]{6}[0-9a-z]{2}([0-9a-z]{3})?\z/i")
     */
    private $bic;

    /**
     * @ORM\ManyToOne(targetEntity=Media::class, inversedBy="companies", cascade={"persist"})
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    private $media;

    /**
     * @ORM\ManyToOne(targetEntity=Address::class, inversedBy="companies", cascade={"persist"})
     * @Assert\Type(type="App\Entity\Address")
     * @Assert\Valid()
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

    /**
     * @ORM\OneToMany(targetEntity=CompanyTheme::class, mappedBy="company")
     */
    private $companyThemes;

    /**
     * @ORM\OneToMany(targetEntity=Model::class, mappedBy="company")
     */
    private $models;

    /**
     * @ORM\OneToMany(targetEntity=DriverOrder::class, mappedBy="driver")
     */
    private $driverOrders;

    public function __construct()
    {
        $this->companyMaterials = new ArrayCollection();
        $this->companyExtras = new ArrayCollection();
        $this->companyPaintings = new ArrayCollection();
        $this->companyThemes = new ArrayCollection();
        $this->models = new ArrayCollection();
        $this->driverOrders = new ArrayCollection();
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

    /**
     * @return Collection|CompanyTheme[]
     */
    public function getCompanyThemes(): Collection
    {
        return $this->companyThemes;
    }

    public function addCompanyTheme(CompanyTheme $companyTheme): self
    {
        if (!$this->companyThemes->contains($companyTheme)) {
            $this->companyThemes[] = $companyTheme;
            $companyTheme->setCompany($this);
        }

        return $this;
    }

    public function removeCompanyTheme(CompanyTheme $companyTheme): self
    {
        if ($this->companyThemes->removeElement($companyTheme)) {
            // set the owning side to null (unless already changed)
            if ($companyTheme->getCompany() === $this) {
                $companyTheme->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Model[]
     */
    public function getModels(): Collection
    {
        return $this->models;
    }

    public function addModel(Model $model): self
    {
        if (!$this->models->contains($model)) {
            $this->models[] = $model;
            $model->setCompany($this);
        }

        return $this;
    }

    public function removeModel(Model $model): self
    {
        if ($this->models->removeElement($model)) {
            // set the owning side to null (unless already changed)
            if ($model->getCompany() === $this) {
                $model->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|DriverOrder[]
     */
    public function getDriverOrders(): Collection
    {
        return $this->driverOrders;
    }

    public function addDriverOrder(DriverOrder $driverOrder): self
    {
        if (!$this->driverOrders->contains($driverOrder)) {
            $this->driverOrders[] = $driverOrder;
            $driverOrder->setDriver($this);
        }

        return $this;
    }

    public function removeDriverOrder(DriverOrder $driverOrder): self
    {
        if ($this->driverOrders->removeElement($driverOrder)) {
            // set the owning side to null (unless already changed)
            if ($driverOrder->getDriver() === $this) {
                $driverOrder->setDriver(null);
            }
        }

        return $this;
    }
}
