<?php

namespace App\Controller;

use App\Entity\Pelicula;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/pelicula', name: 'app_pelicula')]
final class PeliculaController extends AbstractController
{
   
    //GET 127.0.0.1:8000/pelicula
    #[Route('/', name: 'app_pelicula_list', methods: ['GET'])]
    public function list(EntityManagerInterface $eni): Response
    {
        $peliculas = $eni->getRepository(Pelicula::class)->findAll();
        if (empty($peliculas)) {
            return $this->json("No hay peliculas", 404);
        }
        $peliculasJson = array();
        foreach ($peliculas as $pelicula) {
            $peliculasJson[] = [
                "id" => $pelicula->getId(),
                "titulo" => $pelicula->getTitulo(),
                "tituloOriginal" => $pelicula->getTituloOriginal(),
                "descripcion" => $pelicula->getDescripcion(),
                "fechaSalida" => $pelicula->getFechaSalida(),
                "borrado" => $pelicula->isBorrado(),
            ];
        }
        return $this->json($peliculasJson, 200);
    }

    //Buscar pelicula por id
    //GET 127.0.0.1:8000/pelicula/3
    #[Route('/{id}', name: 'app_pelicula_id', methods: ['GET'])]
    public function peliculaId(int $id, EntityManagerInterface $eni): Response
    {
        $peliculas = $eni->getRepository(Pelicula::class)->find($id);

        if (empty($peliculas)) {
            return $this->json("No hay peliculas", 404);
        }
        //Devolverá  un solo elemento
        $peliculasJson[] = [
             "id" => $peliculas->getId(),
                "titulo" => $peliculas->getTitulo(),
                "tituloOriginal" => $peliculas->getTituloOriginal(),
                "descripcion" => $peliculas->getDescripcion(),
                "fechaSalida" => $peliculas->getFechaSalida(),
                "borrado" => $peliculas->isBorrado(),
        ];

        return $this->json($peliculasJson, 200);
    }

    //Borrar pelicula por id
    //POST 127.0.0.1:8000/pelicula/id
    #[Route('/{id}', name: 'app_pelicula_borrar', methods: ['POST'])]
    public function borrar(int $id, EntityManagerInterface $eni): Response
    {

        $peliculas = $eni->getRepository(Pelicula::class)->find($id);

        if (empty($peliculas)) {
            return $this->json("No existe esta pelicula", 404);
        }
        //Devolverá  un solo elemento
        $eni->remove($peliculas);
        $eni->flush();
        return $this->json("pelicula borrado", 200);
    }


    //modificar pelicula
    //PUT 127.0.0.1:8000/pelicula
    #[Route('/{id}', name: 'app_pelicula_modificar', methods: ['PUT'])]
    public function modificar(int $id, EntityManagerInterface $eni, Request $request): Response
    {

        $pelicula = $eni->getRepository(Pelicula::class)->find($id);

        if (empty($pelicula)) {
            return $this->json("No existe esta pelicula", 404);
        }

        $data = json_decode($request->getContent(), true);


        if (isset($data["titulo"])) {
            $pelicula->setTitulo($data["titulo"]);
        }
        if (isset($data["tituloOriginal"])) {
            $pelicula->setTituloOriginal($data["tituloOriginal"]);
        }
        if (isset($data["descripcion"])) {
            $pelicula->setDescripcion($data["descripcion"]);
        }
        if (isset($data["fechaSalida"])) {
            $pelicula->setFechaSalida($data["fechaSalida"]);
        }
        if (isset($data["borrado"])) {
            $pelicula->setBorrado($data["borrado"]);
        }

        $eni->persist($pelicula);
        $eni->flush();

        return $this->json("pelicula modificado", 200);
    }
}
