<?php

namespace App\Entity;

use App\Repository\ViewsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ViewsRepository::class)
 */
class Views
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbView;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $movieId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbView(): ?int
    {
        return $this->nbView;
    }

    public function setNbView(int $nbView): self
    {
        $this->nbView = $nbView;

        return $this;
    }

    public function getMovieId(): ?string
    {
        return $this->movieId;
    }

    public function setMovieId(?string $movieId): self
    {
        $this->movieId = $movieId;

        return $this;
    }
}
