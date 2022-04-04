<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\DriverOrderRepository;

/**
 * @ORM\Entity(repositoryClass=DriverOrderRepository::class)
 */
class DriverOrder
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="driverOrders")
     */
    private $driver;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="driverOrders")
     */
    private $command;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDriver(): ?Company
    {
        return $this->driver;
    }

    public function setDriver(?Company $driver): self
    {
        $this->driver = $driver;

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
}
