<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
class Usuario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 100)]
    private ?string $correo = null;

    #[ORM\Column(length: 30)]
    private ?string $nombre = null;

    #[ORM\Column(length: 100)]
    private ?string $contrasena = null;

    #[ORM\Column]
    private ?int $tipo = null;

    /**
     * @var Collection<int, EstadoPelicula>
     */
    #[ORM\OneToMany(targetEntity: EstadoPelicula::class, mappedBy: 'usuario')]
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

    public function getCorreo(): ?string
    {
        return $this->correo;
    }

    public function setCorreo(string $correo): static
    {
        $this->correo = $correo;

        return $this;
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

    public function getContrasena(): ?string
    {
        return $this->contrasena;
    }

    public function setContrasena(string $contrasena): static
    {
        $this->contrasena = $contrasena;

        return $this;
    }

    public function getTipo(): ?int
    {
        return $this->tipo;
    }

    public function setTipo(int $tipo): static
    {
        $this->tipo = $tipo;

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
            $estadoPelicula->setUsuario($this);
        }

        return $this;
    }

    public function removeEstadoPelicula(EstadoPelicula $estadoPelicula): static
    {
        if ($this->estadoPeliculas->removeElement($estadoPelicula)) {
            // set the owning side to null (unless already changed)
            if ($estadoPelicula->getUsuario() === $this) {
                $estadoPelicula->setUsuario(null);
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
