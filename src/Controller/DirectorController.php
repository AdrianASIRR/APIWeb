<?php

namespace App\Controller;

use App\Entity\Director;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/director', name: 'app_director')]
final class DirectorController extends AbstractController
{
    
    //GET 127.0.0.1:8000/director
    #[Route('/', name: 'app_director_list', methods: ['GET'])]
    public function list(EntityManagerInterface $eni): Response
    {
        $directores = $eni->getRepository(Director::class)->findAll();
        if (empty($directores)) {
            return $this->json("No hay directores", 404);
        }
        $directoresJson = array();
        foreach ($directores as $director) {
            $directoresJson[] = [
                "id" => $director->getId(),
                "nombre" => $director->getNombre(),
                "nacimiento" => $director->getNacimiento()
            ];
        }
        return $this->json($directoresJson, 200);
    }

    //Buscar director por id
    //GET 127.0.0.1:8000/director/3
    #[Route('/{id}', name: 'app_director_id', methods: ['GET'])]
    public function directorId(int $id, EntityManagerInterface $eni): Response
    {
        $directores = $eni->getRepository(Director::class)->find($id);

        if (empty($directores)) {
            return $this->json("No hay directores", 404);
        }
        //Devolverá  un solo elemento
        $directoresJson[] = [
            "id" => $directores->getId(),
            "nombre" => $directores->getNombre(),
            "nacimiento" => $directores->getNacimiento()
        ];

        return $this->json($directoresJson, 200);
    }

    //Borrar director por id
    //POST 127.0.0.1:8000/director/id
    #[Route('/{id}', name: 'app_director_borrar', methods: ['POST'])]
    public function borrar(int $id, EntityManagerInterface $eni): Response
    {

        $directores = $eni->getRepository(Director::class)->find($id);

        if (empty($directores)) {
            return $this->json("No existe esta actor", 404);
        }
        //Devolverá  un solo elemento
        $eni->remove($directores);
        $eni->flush();
        return $this->json("director borrado", 200);
    }


    //modificar director
    //PUT 127.0.0.1:8000/director
    #[Route('/{id}', name: 'app_director_modificar', methods: ['PUT'])]
    public function modificar(int $id, EntityManagerInterface $eni, Request $request): Response
    {

        $director = $eni->getRepository(Director::class)->find($id);

        if (empty($director)) {
            return $this->json("No existe este director", 404);
        }

        $data = json_decode($request->getContent(), true);


        if (isset($data["nombre"])) {
            $director->setNombre($data["nombre"]);
        }
        if (isset($data["nacimiento"])) {
            $director->setNacimiento($data["nacimiento"]);
        }

        $eni->persist($director);
        $eni->flush();

        return $this->json("director modificado", 200);
    }
}
