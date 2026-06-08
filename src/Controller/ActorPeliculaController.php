<?php

namespace App\Controller;

use App\Entity\ActorPelicula;
use App\Repository\ActorPeliculaRepository;
use App\Repository\ActorRepository;
use App\Repository\PeliculaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/actor-pelicula', name: 'app_actor_pelicula')]
final class ActorPeliculaController extends AbstractController
{

    //Crear actor pelicula
    //POST 127.0.0.1:8000/actor-pelicula/
    #[Route('/', name: 'app_actor_pelicula_crear', methods: ['POST'])]
    public function crear(
        Request $request,
        EntityManagerInterface $emi,
        PeliculaRepository $peliculaRepository,
        ActorRepository $actorRepository,
    ): Response {
        // 1. Decodificar los datos JSON recibidos
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['pelicula'], $data['actor'])) {
            return $this->json('Faltan parámetros obligatorios.', 400);
        }

        // 2. Buscar las entidades relacionadas
        $pelicula = $peliculaRepository->find($data['pelicula']);
        $actor = $actorRepository->find($data['actor']);

        // Evitamos que Doctrine pete si los IDs no corresponden a ningún registro real
        if (!$pelicula) {
            return $this->json('La película especificada no existe.', 404);
        }
        if (!$actor) {
            return $this->json('El actor especificado no existe.', 404);
        }
        // 3. Opcional: Verificar si ya existe este estado para evitar duplicar la clave compuesta
        $existe = $emi->getRepository(ActorPelicula::class)->find([
            'pelicula' => $pelicula,
            'actor' => $actor
        ]);

        if ($existe) {
            return $this->json('El estado de la película ya existe para este actor.', 409);
        }

        // 4. Crear y configurar la nueva entidad ActorPelicula
        $actorPelicula = new ActorPelicula();
        $actorPelicula->setPelicula($pelicula);
        $actorPelicula->setActor($actor);


        $emi->persist($actorPelicula);
        $emi->flush();

        return $this->json("Actor de pelicula creado", 201);
    }

    //  Buscar por id compuesta
    //  GET 127.0.0.1:8000/actor-pelicula/concreta/4/12 (Pelicula 4, Actor 12)
    #[Route('/concreta/{peliculaId}/{actorId}', name: 'app_actor_pelicula_concreta', methods: ['GET'])]
    public function getEstadoPelicula(int $peliculaId, int $actorId, ActorPeliculaRepository $repository): Response
    {
        // Buscamos empleando la clave compuesta como un array asociativo
        $actorPelicula = $repository->find(['pelicula' => $peliculaId, 'actor' => $actorId]);

        // Verificamos si existe y si no está marcado como borrado lógico
        if (!$actorPelicula) {
            return $this->json("No hay actorPelicula", 404);
        }
        //Devolverá  un solo elemento
        $actorPeliculaJson = [
            "id_compuesto" => $actorPelicula->getCompoundId(),
            "pelicula" => [
                'id' => $actorPelicula->getPelicula()->getId(),
                'titulo' => $actorPelicula->getPelicula()->getTitulo()
            ],
            "actor" => [
                'id' => $actorPelicula->getActor()->getId(),
                'nombre' => $actorPelicula->getActor()->getNombre()
            ]
        ];

        return $this->json($actorPeliculaJson, 200);
    }

    //  Buscar por id actor
    //  GET 127.0.0.1:8000/actor-pelicula/actor/5 
    #[Route('/actor/{actorId}', name: 'app_actor_pelicula_actor', methods: ['GET'])]
    public function getEstadosActor(int $actorId, ActorPeliculaRepository $repository): Response
    {
        // Buscamos empleando la clave compuesta como un array asociativo
        $actorPeliculas = $repository->findBy(['actor' => $actorId]);

        // Verificamos si existe y si no está marcado como borrado lógico
        if (!$actorPeliculas) {
            return $this->json("No hay actorPelicula", 404);
        }

        $baseUrl = 'http://127.0.0.1:8000/imagenes/pelicula/';


        $actorPeliculasJson = array();
        foreach ($actorPeliculas as $actorPelicula) {
            $fotoUrl = $actorPelicula->getPelicula()->getImagenRuta() ? $baseUrl . $actorPelicula->getPelicula()->getImagenRuta() : $baseUrl . "placeholder.jpg";

            $actorPeliculasJson[] = [
                
                // "id_compuesto" => $generoPelicula->getCompoundId(),
                // "pelicula" => [
                'idPelicula' => $actorPelicula->getPelicula()->getId(),
                'titulo' => $actorPelicula->getPelicula()->getTitulo(),
                'imagenRuta' => $fotoUrl
                // ],
                // "genero" => [
                //     'id' => $generoPelicula->getGenero()->getId(),
                //     'nombre' => $generoPelicula->getGenero()->getNombre()
                // ]
            ];
        }

        return $this->json($actorPeliculasJson, 200);
    }

    //  Buscar por id pelicula 
    //  GET 127.0.0.1:8000/actor-pelicula/pelicula/5
    #[Route('/pelicula/{peliculaId}', name: 'app_actor_pelicula_peli', methods: ['GET'])]
    public function getEstadosPelicula(int $peliculaId, ActorPeliculaRepository $repository): Response
    {
        // Buscamos empleando la clave compuesta como un array asociativo
        $actorPeliculas = $repository->findBy(['pelicula' => $peliculaId]);

        // Verificamos si existe y si no está marcado como borrado lógico
        if (!$actorPeliculas) {
            return $this->json("No hay actorPelicula", 404);
        }

        $actorPeliculasJson = array();
        foreach ($actorPeliculas as $actorPelicula) {
            $actorPeliculasJson[] = [
                "id_compuesto" => $actorPelicula->getCompoundId(),
                "pelicula" => [
                    'id' => $actorPelicula->getPelicula()->getId(),
                    'titulo' => $actorPelicula->getPelicula()->getTitulo()
                ],
                "actor" => [
                    'id' => $actorPelicula->getActor()->getId(),
                    'nombre' => $actorPelicula->getActor()->getNombre()
                ]
            ];
        }

        return $this->json($actorPeliculasJson, 200);
    }
}
