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
        // $actores = $eni->getRepository(Actor::class)->findAll();
        $actores = $eni->getRepository(Actor::class)->findBy(['borrado' => false]);

        if (empty($actores)) {
            return $this->json("No hay actores", 404);
        }
        $actoresJson = array();
        foreach ($actores as $actor) {
            $actoresJson[] = [
                "id" => $actor->getId(),
                "nombre" => $actor->getNombre(),
                "nacimiento" => $actor->getNacimiento(),
                "borrado" => $actor->isBorrado(),
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
        $actoresJson = [
            "id" => $actores->getId(),
            "nombre" => $actores->getNombre(),
            "nacimiento" => $actores->getNacimiento(),
            "borrado" => $actores->isBorrado()
        ];

        return $this->json($actoresJson, 200);
    }

    //Crear actor
    //POST 127.0.0.1:8000/actor/
    #[Route('/', name: 'app_actor_crear', methods: ['POST'])]
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
        $actor = new Actor();
        $actor->setNombre($data["nombre"]);

        if (isset($data["nacimiento"]) && !empty($data["nacimiento"])) {
            try {
                // 🌟 Convertimos el string 'YYYY-MM-DD' de Angular en un objeto DateTime de PHP
                $fecha = new \DateTime($data["nacimiento"]);
                $actor->setNacimiento($fecha);
            } catch (\Exception $e) {
                return $this->json("Formato de fecha no válido. Use YYYY-MM-DD", 400);
            }
        }

        $eni->persist($actor);
        $eni->flush();

        return $this->json("Actor creado", 201);
    }

    //Borrar actor por id
    //POST 127.0.0.1:8000/actor/21
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
    //PUT 127.0.0.1:8000/actor/34
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

        if (isset($data["nacimiento"]) && !empty($data["nacimiento"])) {
            try {
                // 🌟 Convertimos el string 'YYYY-MM-DD' de Angular en un objeto DateTime de PHP
                $fecha = new \DateTime($data["nacimiento"]);
                $actor->setNacimiento($fecha);
            } catch (\Exception $e) {
                return $this->json("Formato de fecha no válido. Use YYYY-MM-DD", 400);
            }
        } else {
            $actor->setNacimiento(null);
        }

        $eni->persist($actor);
        $eni->flush();

        return $this->json("Actor modificado", 200);
    }

    //Borrado lógico actor
    //PUT 127.0.0.1:8000/actor/blogico/4
    #[Route('/blogico/{id}', name: 'app_actor_borrado_logico', methods: ['PUT'])]
    public function borradoLogico(int $id, EntityManagerInterface $eni, Request $request): Response
    {

        $actor = $eni->getRepository(Actor::class)->find($id);

        if (empty($actor)) {
            return $this->json("No existe este actor", 404);
        }

        $data = json_decode($request->getContent(), true);

        $actor->setBorrado(1);

        $eni->persist($actor);
        $eni->flush();

        return $this->json("Actor borrado lógicamente", 200);
    }
}
