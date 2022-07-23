<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\CompanyExtraRepository;

/**
 * @ORM\Entity(repositoryClass=CompanyExtraRepository::class)
 */
class CompanyExtra
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="companyExtras")
     */
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity=Extra::class, inversedBy="companyExtras")
     */
    private $extra;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getExtra(): ?Extra
    {
        return $this->extra;
    }

    public function setExtra(?Extra $extra): self
    {
        $this->extra = $extra;

        return $this;
    }

    public function __toString(): string
    {
        return $this->extra->getName();
    }
}
