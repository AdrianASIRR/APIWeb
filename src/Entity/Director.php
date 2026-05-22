<?php

namespace App\Entity;

use App\Repository\DirectorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DirectorRepository::class)]
class Director
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
     * @var Collection<int, DirectorPelicula>
     */
    #[ORM\OneToMany(targetEntity: DirectorPelicula::class, mappedBy: 'director')]
    private Collection $directorPeliculas;

    #[ORM\Column(options: ['default' => false])]
    private bool $borrado = false;

    public function __construct()
    {
        $this->directorPeliculas = new ArrayCollection();
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
     * @return Collection<int, DirectorPelicula>
     */
    public function getDirectorPeliculas(): Collection
    {
        return $this->directorPeliculas;
    }

    public function addDirectorPelicula(DirectorPelicula $directorPelicula): static
    {
        if (!$this->directorPeliculas->contains($directorPelicula)) {
            $this->directorPeliculas->add($directorPelicula);
            $directorPelicula->setDirector($this);
        }

        return $this;
    }

    public function removeDirectorPelicula(DirectorPelicula $directorPelicula): static
    {
        if ($this->directorPeliculas->removeElement($directorPelicula)) {
            // set the owning side to null (unless already changed)
            if ($directorPelicula->getDirector() === $this) {
                $directorPelicula->setDirector(null);
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
