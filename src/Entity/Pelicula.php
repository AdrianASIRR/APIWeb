<?php

namespace App\Entity;

use App\Repository\PeliculaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PeliculaRepository::class)]
class Pelicula
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $titulo = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $tituloOriginal = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $fechaSalida = null;

    #[ORM\Column]
    private ?bool $borrado = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): static
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getTituloOriginal(): ?string
    {
        return $this->tituloOriginal;
    }

    public function setTituloOriginal(?string $tituloOriginal): static
    {
        $this->tituloOriginal = $tituloOriginal;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): static
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getFechaSalida(): ?\DateTime
    {
        return $this->fechaSalida;
    }

    public function setFechaSalida(?\DateTime $fechaSalida): static
    {
        $this->fechaSalida = $fechaSalida;

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
