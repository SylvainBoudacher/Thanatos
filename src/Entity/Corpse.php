<?php

namespace App\Entity;

use Carbon\Carbon;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CorpseRepository;
use App\Entity\Traits\TimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as AcmeAssert;

/**
 * @ORM\Entity(repositoryClass=CorpseRepository::class)
 */
class  Corpse
{
    use TimestampableTrait;

    public const WOMEN = "Femme";
    public const MAN = "Homme";
    public const OTHER = "Autre";

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 2,
     *      max = 255,
     * )
     * @Assert\Regex("/^[a-z][a-z- ]+[a-z]$/i")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotNull
     * @Assert\Length(
     *      min = 2,
     *      max = 255,
     * )
     * @Assert\Regex("/^[a-z][a-z- ]+[a-z]$/i")
     */
    private $lastname;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\NotNull
     */
    private $birthdate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\NotNull
     * @var string A "Y-m-d H:i:s" formatted value
     * @AcmeAssert\LessThanOrEqual()
     */
    private $dayOfDeath;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     * @Assert\NotNull
     * @Assert\Length(
     *      min = 5,
     *      max = 5,
     * )
     */
    private $sex;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 10,
     *      max = 5000,
     * )
     */
    private $causeOfDeath;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\Positive
     * @Assert\NotBlank
     */
    private $weight;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\Positive
     * @Assert\NotBlank
     */
    private $height;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="corpses")
     */
    private $command;

    /**
     * @ORM\ManyToOne(targetEntity=Media::class, inversedBy="corpses", cascade={"persist"})
     */
    private $media;

    /**
     * @ORM\ManyToOne(targetEntity=Warehouse::class, inversedBy="corpses")
     */
    private $warehouse;

    /**
     * @ORM\OneToOne(targetEntity=Preparation::class, cascade={"persist", "remove"}, fetch="EAGER")
     */
    private $preparation;

    /**
     * @ORM\Column(type="integer", options={"default": "0"})
     */
    private $position;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getBirthdate(): ?DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(?DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getDayOfDeath(): ?DateTimeInterface
    {
        return $this->dayOfDeath;
    }

    public function setDayOfDeath(?DateTimeInterface $dayOfDeath): self
    {
        $this->dayOfDeath = $dayOfDeath;

        return $this;
    }

    public function getSex(): ?string
    {
        return $this->sex;
    }

    public function setSex(?string $sex): self
    {
        $this->sex = $sex;

        return $this;
    }

    public function getCauseOfDeath(): ?string
    {
        return $this->causeOfDeath;
    }

    public function setCauseOfDeath(?string $causeOfDeath): self
    {
        $this->causeOfDeath = $causeOfDeath;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(?float $height): self
    {
        $this->height = $height;

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

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): self
    {
        $this->media = $media;

        return $this;
    }

    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse(?Warehouse $warehouse): self
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    public function checkDateConsistency(): bool
    {
        $dayOfDeath = new Carbon($this->dayOfDeath);
        $birthdate = new Carbon($this->birthdate);

        return $dayOfDeath->gte($birthdate) && $dayOfDeath->lt(Carbon::now());
    }

    public function isBirthdateValid(): bool
    {
        $birthdate = new Carbon($this->birthdate);
        return $birthdate->lt(Carbon::now());
    }

    public function getPreparation(): ?Preparation
    {
        return $this->preparation;
    }

    public function setPreparation(?Preparation $preparation): self
    {
        $this->preparation = $preparation;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

}
