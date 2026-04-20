<?php

namespace App\Controller;

use App\Entity\Genero;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/genero', name: 'app_genero')]
final class GeneroController extends AbstractController
{
    //GET 127.0.0.1:8000/genero
    #[Route('/', name: 'app_genero_list', methods: ['GET'])]
    public function list(EntityManagerInterface $eni): Response
    {
        $generos = $eni->getRepository(Genero::class)->findAll();
        if (empty($generos)) {
            return $this->json("No hay genero", 404);
        }
        $generosJson = array();
        foreach ($generos as $genero) {
            $generosJson[] = [
                "id" => $genero->getId(),
                "nombre" => $genero->getNombre()
            ];
        }
        return $this->json($generosJson, 200);
    }

    //Buscar genero por id
    //GET 127.0.0.1:8000/genero/3
    #[Route('/{id}', name: 'app_genero_id', methods: ['GET'])]
    public function generoId(int $id, EntityManagerInterface $eni): Response
    {
        $generos = $eni->getRepository(Genero::class)->find($id);

        if (empty($generos)) {
            return $this->json("No hay generos", 404);
        }
        //Devolverá  un solo elemento
        $generosJson[] = [
            "id" => $generos->getId(),
            "nombre" => $generos->getNombre()
        ];

        return $this->json($generosJson, 200);
    }

    //Borrar genero por id
    //POST 127.0.0.1:8000/genero/id
    #[Route('/{id}', name: 'app_genero_borrar', methods: ['POST'])]
    public function borrar(int $id, EntityManagerInterface $eni): Response
    {

        $generos = $eni->getRepository(Genero::class)->find($id);

        if (empty($generos)) {
            return $this->json("No existe este genero", 404);
        }
        //Devolverá  un solo elemento
        $eni->remove($generos);
        $eni->flush();
        return $this->json("genero borrado", 200);
    }


    //modificar genero
    //PUT 127.0.0.1:8000/genero
    #[Route('/{id}', name: 'app_genero_modificar', methods: ['PUT'])]
    public function modificar(int $id, EntityManagerInterface $eni, Request $request): Response
    {

        $genero = $eni->getRepository(Genero::class)->find($id);

        if (empty($genero)) {
            return $this->json("No existe este genero", 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data["nombre"])) {
            $genero->setNombre($data["nombre"]);
        }
        
        $eni->persist($genero);
        $eni->flush();

        return $this->json("estado modificado", 200);
    }
   
}
