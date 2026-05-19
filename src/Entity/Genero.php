<?php

namespace App\Entity;

use App\Repository\GeneroRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GeneroRepository::class)]
class Genero
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nombre = null;

    /**
     * @var Collection<int, GeneroPelicula>
     */
    #[ORM\OneToMany(targetEntity: GeneroPelicula::class, mappedBy: 'genero')]
    private Collection $generoPeliculas;

    #[ORM\Column]
    private ?bool $borrado = null;

    public function __construct()
    {
        $this->generoPeliculas = new ArrayCollection();
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

    /**
     * @return Collection<int, GeneroPelicula>
     */
    public function getGeneroPeliculas(): Collection
    {
        return $this->generoPeliculas;
    }

    public function addGeneroPelicula(GeneroPelicula $generoPelicula): static
    {
        if (!$this->generoPeliculas->contains($generoPelicula)) {
            $this->generoPeliculas->add($generoPelicula);
            $generoPelicula->setGenero($this);
        }

        return $this;
    }

    public function removeGeneroPelicula(GeneroPelicula $generoPelicula): static
    {
        if ($this->generoPeliculas->removeElement($generoPelicula)) {
            // set the owning side to null (unless already changed)
            if ($generoPelicula->getGenero() === $this) {
                $generoPelicula->setGenero(null);
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
