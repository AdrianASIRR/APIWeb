<?php

namespace App\Entity;

use App\Repository\EstadoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EstadoRepository::class)]
class Estado
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $nombre = null;

    /**
     * @var Collection<int, EstadoPelicula>
     */
    #[ORM\OneToMany(targetEntity: EstadoPelicula::class, mappedBy: 'estado')]
    private Collection $estadoPeliculas;

    #[ORM\Column]
    private ?bool $borrado = null;

    public function __construct()
    {
        $this->estadoPeliculas = new ArrayCollection();
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
     * @return Collection<int, EstadoPelicula>
     */
    public function getEstadoPeliculas(): Collection
    {
        return $this->estadoPeliculas;
    }

    public function addEstadoPelicula(EstadoPelicula $estadoPelicula): static
    {
        if (!$this->estadoPeliculas->contains($estadoPelicula)) {
            $this->estadoPeliculas->add($estadoPelicula);
            $estadoPelicula->setEstado($this);
        }

        return $this;
    }

    public function removeEstadoPelicula(EstadoPelicula $estadoPelicula): static
    {
        if ($this->estadoPeliculas->removeElement($estadoPelicula)) {
            // set the owning side to null (unless already changed)
            if ($estadoPelicula->getEstado() === $this) {
                $estadoPelicula->setEstado(null);
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
