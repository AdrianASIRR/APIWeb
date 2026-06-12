<?php

namespace App\Controller;

use App\Entity\DirectorPelicula;
use App\Repository\DirectorPeliculaRepository;
use App\Repository\DirectorRepository;
use App\Repository\PeliculaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;


#[Route('/director-pelicula', name: 'app_director_pelicula')]
final class DirectorPeliculaController extends AbstractController
{

    //Crear director pelicula
    //POST 127.0.0.1:8000/director-pelicula/
    #[Route('/', name: 'app_director_pelicula_crear', methods: ['POST'])]
    public function crear(
        Request $request,
        EntityManagerInterface $emi,
        PeliculaRepository $peliculaRepository,
        DirectorRepository $directorRepository,
    ): Response {
        // 1. Decodificar los datos JSON recibidos
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['pelicula'], $data['director'])) {
            return $this->json('Faltan parámetros obligatorios.', 400);
        }

        // 2. Buscar las entidades relacionadas
        $pelicula = $peliculaRepository->find($data['pelicula']);
        $director = $directorRepository->find($data['director']);

        // Evitamos que Doctrine pete si los IDs no corresponden a ningún registro real
        if (!$pelicula) {
            return $this->json('La película especificada no existe.', 404);
        }
        if (!$director) {
            return $this->json('El director especificado no existe.', 404);
        }
        // 3. Opcional: Verificar si ya existe este estado para evitar duplicar la clave compuesta
        $existe = $emi->getRepository(DirectorPelicula::class)->find([
            'pelicula' => $pelicula,
            'director' => $director
        ]);

        if ($existe) {
            return $this->json('El estado de la película ya existe para este usuario.', 409);
        }

        // 4. Crear y configurar la nueva entidad DirectorPelicula
        $directorPelicula = new DirectorPelicula();
        $directorPelicula->setPelicula($pelicula);
        $directorPelicula->setDirector($director);


        $emi->persist($directorPelicula);
        $emi->flush();

        return $this->json("Director de pelicula creado", 201);
    }

    //  Buscar por id compuesta
    //  GET 127.0.0.1:8000/director-pelicula/concreta/4/12 (Pelicula 4, Usuario 12)
    #[Route('/concreta/{peliculaId}/{directorId}', name: 'app_director_pelicula_concreta', methods: ['GET'])]
    public function getEstadoPelicula(int $peliculaId, int $directorId, DirectorPeliculaRepository $repository): Response
    {
        // Buscamos empleando la clave compuesta como un array asociativo
        $directorPelicula = $repository->find(['pelicula' => $peliculaId, 'director' => $directorId]);

        // Verificamos si existe y si no está marcado como borrado lógico
        if (!$directorPelicula) {
            return $this->json("No hay directorPelicula", 404);
        }
        //Devolverá  un solo elemento
        $directorPeliculaJson = [
            "id_compuesto" => $directorPelicula->getCompoundId(),
            "pelicula" => [
                'id' => $directorPelicula->getPelicula()->getId(),
                'titulo' => $directorPelicula->getPelicula()->getTitulo()
            ],
            "director" => [
                'id' => $directorPelicula->getDirector()->getId(),
                'nombre' => $directorPelicula->getDirector()->getNombre()
            ]
        ];

        return $this->json($directorPeliculaJson, 200);
    }

    //  Buscar por id director
    //  GET 127.0.0.1:8000/director-pelicula/director/5 
    #[Route('/director/{directorId}', name: 'app_director_pelicula_director', methods: ['GET'])]
    public function getEstadosDirector(int $directorId, DirectorPeliculaRepository $repository): Response
    {
        // Buscamos empleando la clave compuesta como un array asociativo
        $directorPeliculas = $repository->findBy(['director' => $directorId]);

        // Verificamos si existe y si no está marcado como borrado lógico
        if (!$directorPeliculas) {
            return $this->json("No hay directorPelicula", 404);
        }

        $baseUrl = 'http://127.0.0.1:8000/imagenes/pelicula/';


        $directorPeliculasJson = array();
        foreach ($directorPeliculas as $directorPelicula) {
            $fotoUrl = $directorPelicula->getPelicula()->getImagenRuta() ? $baseUrl . $directorPelicula->getPelicula()->getImagenRuta() : $baseUrl . "placeholder.jpg";

            $directorPeliculasJson[] = [

                // "id_compuesto" => $generoPelicula->getCompoundId(),
                // "pelicula" => [
                'idPelicula' => $directorPelicula->getPelicula()->getId(),
                'titulo' => $directorPelicula->getPelicula()->getTitulo(),
                'imagenRuta' => $fotoUrl
                // ],
                // "genero" => [
                //     'id' => $generoPelicula->getGenero()->getId(),
                //     'nombre' => $generoPelicula->getGenero()->getNombre()
                // ]
            ];
        }

        return $this->json($directorPeliculasJson, 200);
    }

    //  Buscar por id pelicula 
    //  GET 127.0.0.1:8000/director-pelicula/pelicula/5
    #[Route('/pelicula/{peliculaId}', name: 'app_director_pelicula_peli', methods: ['GET'])]
    public function getEstadosPelicula(int $peliculaId, DirectorPeliculaRepository $repository): Response
    {
        // Buscamos empleando la clave compuesta como un array asociativo
         $directorPeliculas = $repository->createQueryBuilder('ap')
            ->join('ap.director', 'd') // Forzamos el enlace con la entidad Actor
            ->where('ap.pelicula = :peliculaId')
            ->andWhere('d.borrado = :borradoFalso') // Filtramos directamente en el JOIN del actor
            ->setParameter('peliculaId', $peliculaId)
            ->setParameter('borradoFalso', false)
            ->getQuery()
            ->getResult();

        // Verificamos si existe y si no está marcado como borrado lógico
        if (!$directorPeliculas) {
            return $this->json("No hay directorPelicula", 404);
        }

        $directorPeliculasJson = array();
        foreach ($directorPeliculas as $directorPelicula) {
            $directorPeliculasJson[] = [
                "id_compuesto" => $directorPelicula->getCompoundId(),
                "pelicula" => [
                    'id' => $directorPelicula->getPelicula()->getId(),
                    'titulo' => $directorPelicula->getPelicula()->getTitulo()
                ],
                "director" => [
                    'id' => $directorPelicula->getDirector()->getId(),
                    'nombre' => $directorPelicula->getDirector()->getNombre()
                ]
            ];
        }

        return $this->json($directorPeliculasJson, 200);
    }

    //  Obtener relaciones
    //  GET 127.0.0.1:8000/director-pelicula/
    #[Route('/', name: 'app_director_pelicula_list', methods: ['GET'])]
    public function list(EntityManagerInterface $eni): Response
    {
        // Buscamos por orden de pelicula
        $directoresPelicula = $eni->getRepository(DirectorPelicula::class)
            ->findBy([], ['pelicula' => 'ASC']);

        // Verificamos si existe
        if (!$directoresPelicula) {
            return $this->json("No hay actorPelicula", 404);
        }

        $directoresPeliculaJson = array();
        //Devolverá  un solo elemento
        foreach ($directoresPelicula as $directorPelicula) {
            $directoresPeliculaJson[] = [
                // "id_compuesto" => $actorPelicula->getCompoundId(),
                // "pelicula" => [
                'id1' => $directorPelicula->getPelicula()->getId(),
                'peliculatitulo' => $directorPelicula->getPelicula()->getTitulo(),
                // ],
                // "actor" => [
                'id2' => $directorPelicula->getDirector()->getId(),
                'directornombre' => $directorPelicula->getDirector()->getNombre()
                // ]
            ];
        }


        return $this->json($directoresPeliculaJson, 200);
    }


    // Borrar actor película
    //POST 127.0.0.1:8000/actor-pelicula/2/1
    #[Route('/{peliculaId}/{directorId}', name: 'app_director_pelicula_borrar', methods: ['POST'])]
    public function borrar(int $peliculaId, int $actorId, EntityManagerInterface $eni): Response
    {

        $directorPelicula = $eni->getRepository(DirectorPelicula::class)->find(['pelicula' => $peliculaId, 'director' => $actorId]);

        if (empty($directorPelicula)) {
            return $this->json("No existe esta actor", 404);
        }
        //Devolverá  un solo elemento
        $eni->remove($directorPelicula);
        $eni->flush();
        return $this->json("Actor borrado", 200);
    }
}
