<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\OrderRepository;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    use TimestampableTrait;

    public const NEW = "NEW";
    public const FINISHED = "FINISHED";

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isValid;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $number;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $rate;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $commentary;

    /**
     * @ORM\OneToMany(targetEntity=DriverOrder::class, mappedBy="command")
     */
    private $driverOrders;

    /**
     * @ORM\OneToMany(targetEntity=Corpse::class, mappedBy="command")
     */
    private $corpses;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     */
    private $possessor;

    /**
     * @ORM\OneToMany(targetEntity=Invoice::class, mappedBy="command")
     */
    private $invoices;

    /**
     * @ORM\OneToMany(targetEntity=AddressOrder::class, mappedBy="command")
     */
    private $addressOrders;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;

    public function __construct()
    {
        $this->driverOrders = new ArrayCollection();
        $this->corpses = new ArrayCollection();
        $this->invoices = new ArrayCollection();
        $this->addressOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsValid(): ?bool
    {
        return $this->isValid;
    }

    public function setIsValid(?bool $isValid): self
    {
        $this->isValid = $isValid;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function setRate(?int $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getCommentary(): ?string
    {
        return $this->commentary;
    }

    public function setCommentary(?string $commentary): self
    {
        $this->commentary = $commentary;

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
            $driverOrder->setCommand($this);
        }

        return $this;
    }

    public function removeDriverOrder(DriverOrder $driverOrder): self
    {
        if ($this->driverOrders->removeElement($driverOrder)) {
            // set the owning side to null (unless already changed)
            if ($driverOrder->getCommand() === $this) {
                $driverOrder->setCommand(null);
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
            $corpse->setCommand($this);
        }

        return $this;
    }

    public function removeCorpse(Corpse $corpse): self
    {
        if ($this->corpses->removeElement($corpse)) {
            // set the owning side to null (unless already changed)
            if ($corpse->getCommand() === $this) {
                $corpse->setCommand(null);
            }
        }

        return $this;
    }

    public function getPossessor(): ?User
    {
        return $this->possessor;
    }

    public function setPossessor(?User $possessor): self
    {
        $this->possessor = $possessor;

        return $this;
    }

    /**
     * @return Collection|Invoice[]
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): self
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices[] = $invoice;
            $invoice->setCommand($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): self
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getCommand() === $this) {
                $invoice->setCommand(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AddressOrder[]
     */
    public function getAddressOrders(): Collection
    {
        return $this->addressOrders;
    }

    public function addAddressOrder(AddressOrder $addressOrder): self
    {
        if (!$this->addressOrders->contains($addressOrder)) {
            $this->addressOrders[] = $addressOrder;
            $addressOrder->setCommand($this);
        }

        return $this;
    }

    public function removeAddressOrder(AddressOrder $addressOrder): self
    {
        if ($this->addressOrders->removeElement($addressOrder)) {
            // set the owning side to null (unless already changed)
            if ($addressOrder->getCommand() === $this) {
                $addressOrder->setCommand(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
