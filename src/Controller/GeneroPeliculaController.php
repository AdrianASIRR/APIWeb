<?php

namespace App\Controller;

use App\Entity\GeneroPelicula;
use App\Repository\GeneroPeliculaRepository;
use App\Repository\GeneroRepository;
use App\Repository\PeliculaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/genero-pelicula', name: 'app_genero_pelicula')]
final class GeneroPeliculaController extends AbstractController
{
    //Crear genero pelicula
    //POST 127.0.0.1:8000/genero-pelicula/
    #[Route('/', name: 'app_genero_pelicula_crear', methods: ['POST'])]
    public function crear(
        Request $request,
        EntityManagerInterface $emi,
        PeliculaRepository $peliculaRepository,
        GeneroRepository $generoRepository,
    ): Response {
        // 1. Decodificar los datos JSON recibidos
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['pelicula'], $data['genero'])) {
            return $this->json('Faltan parámetros obligatorios.', 400);
        }

        // 2. Buscar las entidades relacionadas
        $pelicula = $peliculaRepository->find($data['pelicula']);
        $genero = $generoRepository->find($data['genero']);

        // Evitamos que Doctrine pete si los IDs no corresponden a ningún registro real
        if (!$pelicula) {
            return $this->json('La película especificada no existe.', 404);
        }
        if (!$genero) {
            return $this->json('El género especificado no existe.', 404);
        }
        // 3. Opcional: Verificar si ya existe este estado para evitar duplicar la clave compuesta
        $existe = $emi->getRepository(GeneroPelicula::class)->find([
            'pelicula' => $pelicula,
            'genero' => $genero
        ]);

        if ($existe) {
            return $this->json('El estado de la película ya existe para este género.', 409);
        }

        // 4. Crear y configurar la nueva entidad GeneroPelicula
        $generoPelicula = new GeneroPelicula();
        $generoPelicula->setPelicula($pelicula);
        $generoPelicula->setGenero($genero);


        $emi->persist($generoPelicula);
        $emi->flush();

        return $this->json("Género de película creado", 201);
    }

    //  Buscar por id compuesta
    //  GET 127.0.0.1:8000/genero-pelicula/concreta/4/12 (Pelicula 4, Género 12)
    #[Route('/concreta/{peliculaId}/{generoId}', name: 'app_genero_pelicula_concreta', methods: ['GET'])]
    public function getEstadoPelicula(int $peliculaId, int $generoId, GeneroPeliculaRepository $repository): Response
    {
        // Buscamos empleando la clave compuesta como un array asociativo
        $generoPelicula = $repository->find(['pelicula' => $peliculaId, 'genero' => $generoId]);

        // Verificamos si existe y si no está marcado como borrado lógico
        if (!$generoPelicula) {
            return $this->json("No hay generoPelicula", 404);
        }
        //Devolverá  un solo elemento
        $generoPeliculaJson = [
            "id_compuesto" => $generoPelicula->getCompoundId(),
            "pelicula" => [
                'id' => $generoPelicula->getPelicula()->getId(),
                'titulo' => $generoPelicula->getPelicula()->getTitulo()
            ],
            "genero" => [
                'id' => $generoPelicula->getGenero()->getId(),
                'nombre' => $generoPelicula->getGenero()->getNombre()
            ]
        ];

        return $this->json($generoPeliculaJson, 200);
    }

    //  Buscar por id genero
    //  GET 127.0.0.1:8000/genero-pelicula/genero/5
    #[Route('/genero/{generoId}', name: 'app_genero_pelicula_genero', methods: ['GET'])]
    public function getEstadosGenero(int $generoId, GeneroPeliculaRepository $repository): Response
    {
        // Buscamos empleando la clave compuesta como un array asociativo
        $generoPeliculas = $repository->findBy(['genero' => $generoId]);

        // Verificamos si existe y si no está marcado como borrado lógico
        if (!$generoPeliculas) {
            return $this->json("No hay generoPelicula", 404);
        }

        $baseUrl = 'http://127.0.0.1:8000/imagenes/pelicula/';

        $generoPeliculasJson = array();
        foreach ($generoPeliculas as $generoPelicula) {
            $fotoUrl = $generoPelicula->getPelicula()->getImagenRuta() ? $baseUrl . $generoPelicula->getPelicula()->getImagenRuta() : $baseUrl . "placeholder.jpg";

            $generoPeliculasJson[] = [

                // "id_compuesto" => $generoPelicula->getCompoundId(),
                // "pelicula" => [
                'idPelicula' => $generoPelicula->getPelicula()->getId(),
                'titulo' => $generoPelicula->getPelicula()->getTitulo(),
                'imagenRuta' => $fotoUrl
                // ],
                // "genero" => [
                //     'id' => $generoPelicula->getGenero()->getId(),
                //     'nombre' => $generoPelicula->getGenero()->getNombre()
                // ]
            ];
        }

        return $this->json($generoPeliculasJson, 200);
    }

    //  Buscar por id pelicula 
    //  GET 127.0.0.1:8000/genero-pelicula/pelicula/5
    #[Route('/pelicula/{peliculaId}', name: 'app_genero_pelicula_peli', methods: ['GET'])]
    public function getEstadosPelicula(int $peliculaId, GeneroPeliculaRepository $repository): Response
    {
        // Buscamos empleando la clave compuesta como un array asociativo
        $generoPeliculas = $repository->findBy(['pelicula' => $peliculaId]);

        // Verificamos si existe y si no está marcado como borrado lógico
        if (!$generoPeliculas) {
            return $this->json("No hay generoPelicula", 404);
        }

        $generoPeliculasJson = array();
        foreach ($generoPeliculas as $generoPelicula) {
            $generoPeliculasJson[] = [
                "id_compuesto" => $generoPelicula->getCompoundId(),
                "pelicula" => [
                    'id' => $generoPelicula->getPelicula()->getId(),
                    'titulo' => $generoPelicula->getPelicula()->getTitulo()
                ],
                "genero" => [
                    'id' => $generoPelicula->getGenero()->getId(),
                    'nombre' => $generoPelicula->getGenero()->getNombre()
                ]
            ];
        }

        return $this->json($generoPeliculasJson, 200);
    }

    //  Obtener relaciones
    //  GET 127.0.0.1:8000/genero-pelicula/
    #[Route('/', name: 'app_genero_pelicula_list', methods: ['GET'])]
    public function list(EntityManagerInterface $eni): Response
    {
        // Buscamos por orden de pelicula
        $generosPelicula = $eni->getRepository(GeneroPelicula::class)
            ->findBy([], ['pelicula' => 'ASC']);

        // Verificamos si existe
        if (!$generosPelicula) {
            return $this->json("No hay actorPelicula", 404);
        }

        $generosPeliculaJson = array();
        //Devolverá  un solo elemento
        foreach ($generosPelicula as $generoPelicula) {
            $generosPeliculaJson[] = [
                // "id_compuesto" => $actorPelicula->getCompoundId(),
                // "pelicula" => [
                'id1' => $generoPelicula->getPelicula()->getId(),
                'peliculatitulo' => $generoPelicula->getPelicula()->getTitulo(),
                // ],
                // "actor" => [
                'id2' => $generoPelicula->getGenero()->getId(),
                'generonombre' => $generoPelicula->getGenero()->getNombre()
                // ]
            ];
        }

        return $this->json($generosPeliculaJson, 200);
    }


    // Borrar genero película
    //Borrar genero por id
    //POST 127.0.0.1:8000/genero-pelicula/2/1
    #[Route('/{peliculaId}/{generoId}', name: 'app_genero_pelicula_borrar', methods: ['POST'])]
    public function borrar(int $peliculaId, int $generoId, EntityManagerInterface $eni): Response
    {

        $generosPelicula = $eni->getRepository(GeneroPelicula::class)->find(['pelicula' => $peliculaId, 'genero' => $generoId]);

        if (empty($generosPelicula)) {
            return $this->json("No existe este genero", 404);
        }
        //Devolverá  un solo elemento
        $eni->remove($generosPelicula);
        $eni->flush();
        return $this->json("Genero borrado", 200);
    }
}
