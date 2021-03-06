<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\MediaRepository;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=MediaRepository::class)
 * @Vich\Uploadable
 */
class Media
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /** ! Not related to the ORM !
     * @Vich\UploadableField(mapping="uploads", fileNameProperty="imageName", size="imageSize")
     * @var File|null
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string|null
     */
    private $imageName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int|null
     */
    private $imageSize;

    //TODO: checker la taille d'un lien s'il tiendra sur 255 caractères
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pathname;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isVideo;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="media")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=Company::class, mappedBy="media")
     */
    private $companies;

    /**
     * @ORM\OneToMany(targetEntity=Material::class, mappedBy="media")
     */
    private $materials;

    /**
     * @ORM\OneToMany(targetEntity=Extra::class, mappedBy="media")
     */
    private $extras;

    /**
     * @ORM\OneToMany(targetEntity=Painting::class, mappedBy="media")
     */
    private $paintings;

    /**
     * @ORM\OneToMany(targetEntity=Theme::class, mappedBy="media")
     */
    private $themes;

    /**
     * @ORM\OneToMany(targetEntity=ModelMedia::class, mappedBy="media", orphanRemoval=true)
     */
    private $modelMedia;

    /**
     * @ORM\OneToMany(targetEntity=Corpse::class, mappedBy="media")
     */
    private $corpses;

    /**
     * @ORM\OneToMany(targetEntity=Warehouse::class, mappedBy="media")
     */
    private $warehouses;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->companies = new ArrayCollection();
        $this->materials = new ArrayCollection();
        $this->extras = new ArrayCollection();
        $this->paintings = new ArrayCollection();
        $this->themes = new ArrayCollection();
        $this->modelMedia = new ArrayCollection();
        $this->corpses = new ArrayCollection();
        $this->warehouses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPathname(): ?string
    {
        return $this->pathname;
    }

    public function setPathname(?string $pathname): self
    {
        $this->pathname = $pathname;

        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function getIsVideo(): ?bool
    {
        return $this->isVideo;
    }

    public function setIsVideo(?bool $isVideo): self
    {
        $this->isVideo = $isVideo;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setMedia($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getMedia() === $this) {
                $user->setMedia(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Company[]
     */
    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    public function addCompany(Company $company): self
    {
        if (!$this->companies->contains($company)) {
            $this->companies[] = $company;
            $company->setMedia($this);
        }

        return $this;
    }

    public function removeCompany(Company $company): self
    {
        if ($this->companies->removeElement($company)) {
            // set the owning side to null (unless already changed)
            if ($company->getMedia() === $this) {
                $company->setMedia(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Material[]
     */
    public function getMaterials(): Collection
    {
        return $this->materials;
    }

    public function addMaterial(Material $material): self
    {
        if (!$this->materials->contains($material)) {
            $this->materials[] = $material;
            $material->setMedia($this);
        }

        return $this;
    }

    public function removeMaterial(Material $material): self
    {
        if ($this->materials->removeElement($material)) {
            // set the owning side to null (unless already changed)
            if ($material->getMedia() === $this) {
                $material->setMedia(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Extra[]
     */
    public function getExtras(): Collection
    {
        return $this->extras;
    }

    public function addExtra(Extra $extra): self
    {
        if (!$this->extras->contains($extra)) {
            $this->extras[] = $extra;
            $extra->setMedia($this);
        }

        return $this;
    }

    public function removeExtra(Extra $extra): self
    {
        if ($this->extras->removeElement($extra)) {
            // set the owning side to null (unless already changed)
            if ($extra->getMedia() === $this) {
                $extra->setMedia(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Painting[]
     */
    public function getPaintings(): Collection
    {
        return $this->paintings;
    }

    public function addPainting(Painting $painting): self
    {
        if (!$this->paintings->contains($painting)) {
            $this->paintings[] = $painting;
            $painting->setMedia($this);
        }

        return $this;
    }

    public function removePainting(Painting $painting): self
    {
        if ($this->paintings->removeElement($painting)) {
            // set the owning side to null (unless already changed)
            if ($painting->getMedia() === $this) {
                $painting->setMedia(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Theme[]
     */
    public function getThemes(): Collection
    {
        return $this->themes;
    }

    public function addTheme(Theme $theme): self
    {
        if (!$this->themes->contains($theme)) {
            $this->themes[] = $theme;
            $theme->setMedia($this);
        }

        return $this;
    }

    public function removeTheme(Theme $theme): self
    {
        if ($this->themes->removeElement($theme)) {
            // set the owning side to null (unless already changed)
            if ($theme->getMedia() === $this) {
                $theme->setMedia(null);
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
            $modelMedium->setMedia($this);
        }

        return $this;
    }

    public function removeModelMedium(ModelMedia $modelMedium): self
    {
        if ($this->modelMedia->removeElement($modelMedium)) {
            // set the owning side to null (unless already changed)
            if ($modelMedium->getMedia() === $this) {
                $modelMedium->setMedia(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Corpse[]
     */
    public function getCorpses(): Collection
    {
        return $this->corpses;
    }

    public function addCorpse(Corpse $corpse): self
    {
        if (!$this->corpses->contains($corpse)) {
            $this->corpses[] = $corpse;
            $corpse->setMedia($this);
        }

        return $this;
    }

    public function removeCorpse(Corpse $corpse): self
    {
        if ($this->corpses->removeElement($corpse)) {
            // set the owning side to null (unless already changed)
            if ($corpse->getMedia() === $this) {
                $corpse->setMedia(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Warehouse[]
     */
    public function getWarehouses(): Collection
    {
        return $this->warehouses;
    }

    public function addWarehouse(Warehouse $warehouse): self
    {
        if (!$this->warehouses->contains($warehouse)) {
            $this->warehouses[] = $warehouse;
            $warehouse->setMedia($this);
        }

        return $this;
    }

    public function removeWarehouse(Warehouse $warehouse): self
    {
        if ($this->warehouses->removeElement($warehouse)) {
            // set the owning side to null (unless already changed)
            if ($warehouse->getMedia() === $this) {
                $warehouse->setMedia(null);
            }
        }

        return $this;
    }
}
