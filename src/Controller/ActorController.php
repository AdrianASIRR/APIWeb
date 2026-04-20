<?php

namespace App\Controller;

use App\Entity\Actor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/actor', name: 'app_actor')]
final class ActorController extends AbstractController
{
    //GET 127.0.0.1:8000/actor
    #[Route('/', name: 'app_actor_list', methods: ['GET'])]
    public function list(EntityManagerInterface $eni): Response
    {
        $actores = $eni->getRepository(Actor::class)->findAll();
        if (empty($actores)) {
            return $this->json("No hay actores", 404);
        }
        $actoresJson = array();
        foreach ($actores as $actor) {
            $actoresJson[] = [
                "id" => $actor->getId(),
                "nombre" => $actor->getNombre(),
                "nacimiento" => $actor->getNacimiento()
            ];
        }
        return $this->json($actoresJson, 200);
    }

    //Buscar por id
    //GET 127.0.0.1:8000/actor/3
    #[Route('/{id}', name: 'app_actor_id', methods: ['GET'])]
    public function actorId(int $id, EntityManagerInterface $eni): Response
    {
        $actores = $eni->getRepository(Actor::class)->find($id);

        if (empty($actores)) {
            return $this->json("No hay actores", 404);
        }
        //Devolverá  un solo elemento
        $actoresJson[] = [
            "id" => $actores->getId(),
            "nombre" => $actores->getNombre(),
            "nacimiento" => $actores->getNacimiento()
        ];

        return $this->json($actoresJson, 200);
    }

    //Borrar actor por id
    //POST 127.0.0.1:8000/actor/id
    #[Route('/{id}', name: 'app_actor_borrar', methods: ['POST'])]
    public function borrar(int $id, EntityManagerInterface $eni): Response
    {

        $actores = $eni->getRepository(Actor::class)->find($id);

        if (empty($actores)) {
            return $this->json("No existe esta actor", 404);
        }
        //Devolverá  un solo elemento
        $eni->remove($actores);
        $eni->flush();
        return $this->json("Actor borrado", 200);
    }


    //modificar actor
    //PUT 127.0.0.1:8000/actor
    #[Route('/{id}', name: 'app_actor_modificar', methods: ['PUT'])]
    public function modificar(int $id, EntityManagerInterface $eni, Request $request): Response
    {

        $actor = $eni->getRepository(Actor::class)->find($id);

        if (empty($actor)) {
            return $this->json("No existe este actor", 404);
        }

        $data = json_decode($request->getContent(), true);


        if (isset($data["nombre"])) {
            $actor->setNombre($data["nombre"]);
        }
        if (isset($data["nacimiento"])) {
            $actor->setNacimiento($data["nacimiento"]);
        }

        $eni->persist($actor);
        $eni->flush();

        return $this->json("Actor modificado", 200);
    }
}
