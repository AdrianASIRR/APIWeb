<?php

namespace App\Entity;

use App\Repository\PeliculaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column(options: ['default' => false])]
    private bool $borrado = false;

    /**
     * @var Collection<int, GeneroPelicula>
     */
    #[ORM\OneToMany(targetEntity: GeneroPelicula::class, mappedBy: 'pelicula')]
    private Collection $generoPeliculas;

    /**
     * @var Collection<int, DirectorPelicula>
     */
    #[ORM\OneToMany(targetEntity: DirectorPelicula::class, mappedBy: 'pelicula')]
    private Collection $directorPeliculas;

    /**
     * @var Collection<int, ActorPelicula>
     */
    #[ORM\OneToMany(targetEntity: ActorPelicula::class, mappedBy: 'pelicula')]
    private Collection $actorPeliculas;

    /**
     * @var Collection<int, EstadoPelicula>
     */
    #[ORM\OneToMany(targetEntity: EstadoPelicula::class, mappedBy: 'pelicula')]
    private Collection $estadoPeliculas;

    #[ORM\Column(nullable: true)]
    private ?int $duracion = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagenRuta = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $trailerUrl = null;

    public function __construct()
    {
        $this->generoPeliculas = new ArrayCollection();
        $this->directorPeliculas = new ArrayCollection();
        $this->actorPeliculas = new ArrayCollection();
        $this->estadoPeliculas = new ArrayCollection();
    }

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
            $generoPelicula->setPelicula($this);
        }

        return $this;
    }

    public function removeGeneroPelicula(GeneroPelicula $generoPelicula): static
    {
        if ($this->generoPeliculas->removeElement($generoPelicula)) {
            // set the owning side to null (unless already changed)
            if ($generoPelicula->getPelicula() === $this) {
                $generoPelicula->setPelicula(null);
            }
        }

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
            $directorPelicula->setPelicula($this);
        }

        return $this;
    }

    public function removeDirectorPelicula(DirectorPelicula $directorPelicula): static
    {
        if ($this->directorPeliculas->removeElement($directorPelicula)) {
            // set the owning side to null (unless already changed)
            if ($directorPelicula->getPelicula() === $this) {
                $directorPelicula->setPelicula(null);
            }
        }

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
            $actorPelicula->setPelicula($this);
        }

        return $this;
    }

    public function removeActorPelicula(ActorPelicula $actorPelicula): static
    {
        if ($this->actorPeliculas->removeElement($actorPelicula)) {
            // set the owning side to null (unless already changed)
            if ($actorPelicula->getPelicula() === $this) {
                $actorPelicula->setPelicula(null);
            }
        }

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
            $estadoPelicula->setPelicula($this);
        }

        return $this;
    }

    public function removeEstadoPelicula(EstadoPelicula $estadoPelicula): static
    {
        if ($this->estadoPeliculas->removeElement($estadoPelicula)) {
            // set the owning side to null (unless already changed)
            if ($estadoPelicula->getPelicula() === $this) {
                $estadoPelicula->setPelicula(null);
            }
        }

        return $this;
    }

    public function getDuracion(): ?int
    {
        return $this->duracion;
    }

    public function setDuracion(?int $duracion): static
    {
        $this->duracion = $duracion;

        return $this;
    }

    public function getImagenRuta(): ?string
    {
        return $this->imagenRuta;
    }

    public function setImagenRuta(?string $imagenRuta): static
    {
        $this->imagenRuta = $imagenRuta;

        return $this;
    }

    public function getTrailerUrl(): ?string
    {
        return $this->trailerUrl;
    }

    public function setTrailerUrl(?string $trailerUrl): static
    {
        $this->trailerUrl = $trailerUrl;

        return $this;
    }
}
