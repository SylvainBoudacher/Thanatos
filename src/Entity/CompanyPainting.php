<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\CompanyPaintingRepository;

/**
 * @ORM\Entity(repositoryClass=CompanyPaintingRepository::class)
 */
class CompanyPainting
{
    use TimestampableTrait;
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="companyPaintings")
     */
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity=Painting::class, inversedBy="companyPaintings")
     */
    private $painting;

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

    public function getPainting(): ?Painting
    {
        return $this->painting;
    }

    public function setPainting(?Painting $painting): self
    {
        $this->painting = $painting;

        return $this;
    }
}
