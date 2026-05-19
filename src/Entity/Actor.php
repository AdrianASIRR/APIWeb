<?php

namespace App\Entity;

use App\Repository\ActorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActorRepository::class)]
class Actor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nombre = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $nacimiento = null;

    /**
     * @var Collection<int, ActorPelicula>
     */
    #[ORM\OneToMany(targetEntity: ActorPelicula::class, mappedBy: 'actor')]
    private Collection $actorPeliculas;

    #[ORM\Column]
    private ?bool $borrado = null;

    public function __construct()
    {
        $this->actorPeliculas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getNacimiento(): ?\DateTime
    {
        return $this->nacimiento;
    }

    public function setNacimiento(?\DateTime $nacimiento): static
    {
        $this->nacimiento = $nacimiento;

        return $this;
    }

    /**
     * @return Collection<int, ActorPelicula>
     */
    public function getActorPeliculas(): Collection
    {
        return $this->actorPeliculas;
    }

    public function addActorPelicula(ActorPelicula $actorPelicula): static
    {
        if (!$this->actorPeliculas->contains($actorPelicula)) {
            $this->actorPeliculas->add($actorPelicula);
            $actorPelicula->setActor($this);
        }

        return $this;
    }

    public function removeActorPelicula(ActorPelicula $actorPelicula): static
    {
        if ($this->actorPeliculas->removeElement($actorPelicula)) {
            // set the owning side to null (unless already changed)
            if ($actorPelicula->getActor() === $this) {
                $actorPelicula->setActor(null);
            }
        }

        return $this;
    }

    public function isBorrado(): ?bool
    {
        return $this->borrado;
    }

    public function setBorrado(bool $borrado): static
    {
        $this->borrado = $borrado;

        return $this;
    }
}
