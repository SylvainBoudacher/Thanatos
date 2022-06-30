<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\AddressOrderRepository;

/**
 * @ORM\Entity(repositoryClass=AddressOrderRepository::class)
 */
class AddressOrder
{

    use TimestampableTrait;

    public const DECLARATION_CORPSES = "ADDRESS_DECLARATION_CORPSES";

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Address::class, inversedBy="addressOrders")
     */
    private $address;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="addressOrders")
     */
    private $command;

    /**
     * @ORM\Column(type="string", length=30, options={"default" : "ADDRESS_DECLARATION_CORPSES"})
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCommand(): ?Order
    {
        return $this->command;
    }

    public function setCommand(?Order $command): self
    {
        $this->command = $command;

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
