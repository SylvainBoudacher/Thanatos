<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\WarehouseRepository;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=WarehouseRepository::class)
 */
class Warehouse
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
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $drawersCount;

    /**
     * @ORM\OneToMany(targetEntity=Corpse::class, mappedBy="warehouse")
     */
    private $corpses;

    /**
     * @ORM\ManyToOne(targetEntity=Address::class, inversedBy="warehouses")
     */
    private $address;

    /**
     * @ORM\ManyToOne(targetEntity=Media::class, inversedBy="warehouses")
     */
    private $media;

    public function __construct()
    {
        $this->corpses = new ArrayCollection();
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

    public function getDrawersCount(): ?int
    {
        return $this->drawersCount;
    }

    public function setDrawersCount(?int $drawersCount): self
    {
        $this->drawersCount = $drawersCount;

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
            $corpse->setWarehouse($this);
        }

        return $this;
    }

    public function removeCorpse(Corpse $corpse): self
    {
        if ($this->corpses->removeElement($corpse)) {
            // set the owning side to null (unless already changed)
            if ($corpse->getWarehouse() === $this) {
                $corpse->setWarehouse(null);
            }
        }

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
