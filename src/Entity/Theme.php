<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ThemeRepository;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ThemeRepository::class)
 * @UniqueEntity("name")
 */
class Theme
{
    use TimestampableTrait;

    public const TYPE_CLASSIC = "classic";
    public const TYPE_SPECIAL = "special";


    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique="true")
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 1,
     *      max = 120,
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
     * @ORM\Column(type="string", length=80, nullable=true)
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 7,
     *      max = 7,
     * )
     */
    private $type;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\PositiveOrZero
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity=Media::class, inversedBy="themes", cascade={"persist"})
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    private $media;

    /**
     * @ORM\OneToMany(targetEntity=Preparation::class, mappedBy="theme")
     */
    private $preparations;

    /**
     * @ORM\OneToMany(targetEntity=CompanyTheme::class, mappedBy="theme")
     */
    private $companyThemes;

    public function __construct()
    {
        $this->preparations = new ArrayCollection();
        $this->companyThemes = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

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
            $preparation->setTheme($this);
        }

        return $this;
    }

    public function removePreparation(Preparation $preparation): self
    {
        if ($this->preparations->removeElement($preparation)) {
            // set the owning side to null (unless already changed)
            if ($preparation->getTheme() === $this) {
                $preparation->setTheme(null);
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
            $companyTheme->setTheme($this);
        }

        return $this;
    }

    public function removeCompanyTheme(CompanyTheme $companyTheme): self
    {
        if ($this->companyThemes->removeElement($companyTheme)) {
            // set the owning side to null (unless already changed)
            if ($companyTheme->getTheme() === $this) {
                $companyTheme->setTheme(null);
            }
        }

        return $this;
    }
}
