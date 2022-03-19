<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CreditCardRepository;
use App\Entity\Traits\TimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CreditCardRepository::class)
 */
class CreditCard
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string",length=16, nullable=true)
     * @Assert\NotBlank
     * @Assert\Regex(
     *     pattern="/(^4[0-9]{12}(?:[0-9]{3})?$)|(^(?:5[1-5][0-9]{2}|222[1-9]|22[3-9][0-9]|2[3-6][0-9]{2}|27[01][0-9]|2720)[0-9]{12}$)|(3[47][0-9]{13})|(^3(?:0[0-5]|[68][0-9])[0-9]{11}$)|(^6(?:011|5[0-9]{2})[0-9]{12}$)|(^(?:2131|1800|35\d{3})\d{11}$)/",
     *     message="Votre numéro de carte banquaire n'est pas valide"
     * )
     */
    private $number;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\NotBlank
     */
    private $expirationDate;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     * @Assert\NotBlank
     * @Assert\Regex(
     *     pattern="/^[0-9]{3}$/",
     *     message="Votre CVC n'est pas valide"
     * )
     */
    private $cvc;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank
     * @Assert\Regex(
     *     pattern="/^[a-z ][a-z- ]+[a-z ]+$/i",
     *     message="Votre nom n'est pas valide"
     * )
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="creditCards")
     */
    private $possessor;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(?int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(?\DateTimeInterface $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function getCvc(): ?int
    {
        return $this->cvc;
    }

    public function setCvc(?int $cvc): self
    {
        $this->cvc = $cvc;

        return $this;
    }

    public function getOwner(): ?string
    {
        return $this->owner;
    }

    public function setOwner(?string $owner): self
    {
        $this->owner = $owner;

        return $this;
    }


    public function getUsers(): ?User
    {
        return $this->users;
    }

    public function setUsers(?User $users): self
    {
        $this->users = $users;

        return $this;
    }

    public function getPossessor(): ?User
    {
        return $this->possessor;
    }

    public function setPossessor(?User $possessor)
    {
        $this->possessor = $possessor;
    }
}
