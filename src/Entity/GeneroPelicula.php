<?php

namespace App\Entity;

use App\Repository\GeneroPeliculaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GeneroPeliculaRepository::class)]
class GeneroPelicula
{
    /* #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
 */
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'generoPeliculas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pelicula $pelicula = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'generoPeliculas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Genero $genero = null;


    public function getCompoundId(): string
    {
        return sprintf(
            '%d_%d',
            $this->pelicula->getId(),
            $this->genero->getId()
        );
    }
    /*  public function getId(): ?int
    {
        return $this->id;
    }
 */
    public function getPelicula(): ?Pelicula
    {
        return $this->pelicula;
    }

    public function setPelicula(?Pelicula $pelicula): static
    {
        $this->pelicula = $pelicula;

        return $this;
    }

    public function getGenero(): ?Genero
    {
        return $this->genero;
    }

    public function setGenero(?Genero $genero): static
    {
        $this->genero = $genero;

        return $this;
    }
}
