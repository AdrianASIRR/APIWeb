<?php

namespace App\Entity;

use App\Repository\EstadoPeliculaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EstadoPeliculaRepository::class)]
class EstadoPelicula
{
    /* #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; */

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'estadoPeliculas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pelicula $pelicula = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'estadoPeliculas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $usuario = null;

    #[ORM\ManyToOne(inversedBy: 'estadoPeliculas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Estado $estado = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $puntuacion = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comentario = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $borrado = false;

     public function getCompoundId(): string
    {
        return sprintf(
            '%d_%d',
            $this->pelicula->getId(),
            $this->usuario->getId()
        );
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

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getEstado(): ?Estado
    {
        return $this->estado;
    }

    public function setEstado(?Estado $estado): static
    {
        $this->estado = $estado;

        return $this;
    }

    public function getPuntuacion(): ?int
    {
        return $this->puntuacion;
    }

    public function setPuntuacion(?int $puntuacion): static
    {
        $this->puntuacion = $puntuacion;

        return $this;
    }

    public function getComentario(): ?string
    {
        return $this->comentario;
    }

    public function setComentario(?string $comentario): static
    {
        $this->comentario = $comentario;

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
