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
        // $directores = $eni->getRepository(Director::class)->findAll();
        $directores = $eni->getRepository(Director::class)->findBy(['borrado' => false]);

        if (empty($directores)) {
            return $this->json("No hay directores", 404);
        }
        $directoresJson = array();
        foreach ($directores as $director) {
            $directoresJson[] = [
                "id" => $director->getId(),
                "nombre" => $director->getNombre(),
                "nacimiento" => $director->getNacimiento(),
                "borrado" => $director->isBorrado()
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
        $directoresJson = [
            "id" => $directores->getId(),
            "nombre" => $directores->getNombre(),
            "nacimiento" => $directores->getNacimiento(),
            "borrado" => $directores->isBorrado()
        ];

        return $this->json($directoresJson, 200);
    }

    //Crear director
    //POST 127.0.0.1:8000/director/
    #[Route('/', name: 'app_director_crear', methods: ['POST'])]
    public function crear(request $request, EntityManagerInterface $eni): Response
    {
        $body = $request->getContent();
        $data = json_decode($body, true);

        if (!$data) {
            return $this->json("Error JSON no valido", 404);
        }
        if (!isset($data["nombre"])) {
            return $this->json("No hay nombre", 400);
        }
        $director = new Director();
        $director->setNombre($data["nombre"]);

        if (isset($data["nacimiento"]) && !empty($data["nacimiento"])) {
            try {
                // 🌟 Convertimos el string 'YYYY-MM-DD' de Angular en un objeto DateTime de PHP
                $fecha = new \DateTime($data["nacimiento"]);
                $director->setNacimiento($fecha);
            } catch (\Exception $e) {
                return $this->json("Formato de fecha no válido. Use YYYY-MM-DD", 400);
            }
        }

        $eni->persist($director);
        $eni->flush();

        return $this->json("Director creado", 201);
    }

    //Borrar director por id
    //POST 127.0.0.1:8000/director/id
    #[Route('/{id}', name: 'app_director_borrar', methods: ['POST'])]
    public function borrar(int $id, EntityManagerInterface $eni): Response
    {

        $directores = $eni->getRepository(Director::class)->find($id);

        if (empty($directores)) {
            return $this->json("No existe este director", 404);
        }
        //Devolverá  un solo elemento
        $eni->remove($directores);
        $eni->flush();
        return $this->json("director borrado", 200);
    }


    //modificar director
    //PUT 127.0.0.1:8000/director/23
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
        if (isset($data["nacimiento"]) && !empty($data["nacimiento"])) {
            try {
                // 🌟 Convertimos el string 'YYYY-MM-DD' de Angular en un objeto DateTime de PHP
                $fecha = new \DateTime($data["nacimiento"]);
                $director->setNacimiento($fecha);
            } catch (\Exception $e) {
                return $this->json("Formato de fecha no válido. Use YYYY-MM-DD", 400);
            }
        } else {
            $director->setNacimiento(null);
        }

        $eni->persist($director);
        $eni->flush();

        return $this->json("director modificado", 200);
    }

    //Borrado lógico director
    //PUT 127.0.0.1:8000/director/blogico/4
    #[Route('/blogico/{id}', name: 'app_director_borrado_logico', methods: ['PUT'])]
    public function borradoLogico(int $id, EntityManagerInterface $eni, Request $request): Response
    {

        $director = $eni->getRepository(Director::class)->find($id);

        if (empty($director)) {
            return $this->json("No existe este director", 404);
        }

        $data = json_decode($request->getContent(), true);

        $director->setBorrado(1);

        $eni->persist($director);
        $eni->flush();

        return $this->json("director borrado lógicamente", 200);
    }
}
