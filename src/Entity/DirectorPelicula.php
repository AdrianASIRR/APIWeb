<?php

namespace App\Entity;

use App\Repository\DirectorPeliculaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DirectorPeliculaRepository::class)]
class DirectorPelicula
{
    
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'directorPeliculas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Director $director = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'directorPeliculas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pelicula $pelicula = null;

    public function getCompoundId(): string
    {
        return sprintf(
            '%d_%d',
            $this->director->getId(),
            $this->pelicula->getId()
        );
    }
    public function getDirector(): ?Director
    {
        return $this->director;
    }

    public function setDirector(?Director $director): static
    {
        $this->director = $director;

        return $this;
    }

    public function getPelicula(): ?Pelicula
    {
        return $this->pelicula;
    }

    public function setPelicula(?Pelicula $pelicula): static
    {
        $this->pelicula = $pelicula;

        return $this;
    }
}
