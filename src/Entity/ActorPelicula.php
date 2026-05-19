<?php

namespace App\Entity;

use App\Repository\ActorPeliculaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActorPeliculaRepository::class)]
class ActorPelicula
{
    /* #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; */

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'actorPeliculas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Actor $actor = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'actorPeliculas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pelicula $pelicula = null;

    /*  public function getId(): ?int
    {
        return $this->id;
    }
 */
    public function getCompoundId(): string
    {
        return sprintf(
            '%d_%d',
            $this->actor->getId(),
            $this->pelicula->getId()
        );
    }
    
    public function getActor(): ?Actor
    {
        return $this->actor;
    }

    public function setActor(?Actor $actor): static
    {
        $this->actor = $actor;

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
