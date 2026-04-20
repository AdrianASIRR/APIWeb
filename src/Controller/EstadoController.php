<?php

namespace App\Controller;

use App\Entity\Estado;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/estado', name: 'app_estado')]
final class EstadoController extends AbstractController
{

    //GET 127.0.0.1:8000/estado
    #[Route('/', name: 'app_estado_list', methods: ['GET'])]
    public function list(EntityManagerInterface $eni): Response
    {
        $estados = $eni->getRepository(Estado::class)->findAll();
        if (empty($estados)) {
            return $this->json("No hay estado", 404);
        }
        $estadosJson = array();
        foreach ($estados as $estado) {
            $estadosJson[] = [
                "id" => $estado->getId(),
                "nombre" => $estado->getNombre()
            ];
        }
        return $this->json($estadosJson, 200);
    }

    //Buscar estado por id
    //GET 127.0.0.1:8000/estado/3
    #[Route('/{id}', name: 'app_estado_id', methods: ['GET'])]
    public function estadoId(int $id, EntityManagerInterface $eni): Response
    {
        $estados = $eni->getRepository(Estado::class)->find($id);

        if (empty($estados)) {
            return $this->json("No hay estados", 404);
        }
        //Devolverá  un solo elemento
        $estadosJson[] = [
            "id" => $estados->getId(),
            "nombre" => $estados->getNombre()
        ];

        return $this->json($estadosJson, 200);
    }

    //Borrar estado por id
    //POST 127.0.0.1:8000/estado/id
    #[Route('/{id}', name: 'app_estado_borrar', methods: ['POST'])]
    public function borrar(int $id, EntityManagerInterface $eni): Response
    {

        $estados = $eni->getRepository(Estado::class)->find($id);

        if (empty($estados)) {
            return $this->json("No existe este estado", 404);
        }
        //Devolverá  un solo elemento
        $eni->remove($estados);
        $eni->flush();
        return $this->json("estado borrado", 200);
    }


    //modificar estado
    //PUT 127.0.0.1:8000/estado
    #[Route('/{id}', name: 'app_estado_modificar', methods: ['PUT'])]
    public function modificar(int $id, EntityManagerInterface $eni, Request $request): Response
    {

        $estado = $eni->getRepository(Estado::class)->find($id);

        if (empty($estado)) {
            return $this->json("No existe este estado", 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data["nombre"])) {
            $estado->setNombre($data["nombre"]);
        }
        
        $eni->persist($estado);
        $eni->flush();

        return $this->json("estado modificado", 200);
    }
}
