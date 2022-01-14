<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ModelMediaRepository;
use App\Entity\Traits\TimestampableTrait;

/**
 * @ORM\Entity(repositoryClass=ModelMediaRepository::class)
 */
class ModelMedia
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Model::class, inversedBy="modelMedia")
     */
    private $model;

    /**
     * @ORM\ManyToOne(targetEntity=Media::class, inversedBy="modelMedia")
     */
    private $media;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function setModel(?Model $model): self
    {
        $this->model = $model;

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
