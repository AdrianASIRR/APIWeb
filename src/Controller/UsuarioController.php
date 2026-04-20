<?php

namespace App\Controller;

use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/usuario', name: 'app_usuario')]
final class UsuarioController extends AbstractController
{
    //Crear usuario
    //POST 127.0.0.1:8000/usuario/
    #[Route('/', name: 'app_usuario_crear', methods: ['POST'])]
    public function crear(request $request, EntityManagerInterface $eni): Response
    {
        $body = $request->getContent();
        $data = json_decode($body, true);

        if (!$data) {
            return $this->json("Error JSON no valido", 404);
        }
        if (!isset($data["correo"])) {
            return $this->json("No hay correo", 400);
        }
        if (!isset($data["nombre"])) {
            return $this->json("No hay nombre", 400);
        }
        if (!isset($data["contrasena"])) {
            return $this->json("No hay contrasena", 400);
        }
        $usuario = new usuario();
        $usuario->setNombre($data["nombre"]);
        $usuario->setCorreo($data["correo"]);
        $usuario->setContrasena(password_hash($data["contrasena"], PASSWORD_DEFAULT));
        $usuario->setTipo($data["tipo"]);

        $eni->persist($usuario);
        $eni->flush();

        return $this->json("Usuario creado", 201);
    }

    
    //Borrar usuario por id
    //POST 127.0.0.1:8000/usuario/id
    #[Route('/{id}', name: 'app_usuario_borrar', methods: ['POST'])]
    public function borrar(int $id, EntityManagerInterface $eni): Response
    {

        $usuarios = $eni->getRepository(Usuario::class)->find($id);

        if (empty($usuarios)) {
            return $this->json("No existe esta usuario", 404);
        }
        //Devolverá  un solo elemento
        $eni->remove($usuarios);
        $eni->flush();
        return $this->json("Usuario borrado", 200);
    }

    //Buscar usuario
    //GET 127.0.0.1:8000/usuario/Paco
    #[Route('/nombre/{nombre}', name: 'app_usuario_nombre', methods: ['GET'])]
    public function usuarioNombre(int $nombre, EntityManagerInterface $eni): Response
    {
        $usuarios = $eni->getRepository(Usuario::class)->findBy(['nombre' => $nombre]);

        if (empty($usuarios)) {
            return $this->json("No hay usuarios con ese nombre", 404);
        }
        $usuariosJson = array();
        foreach ($usuarios as $usuario) {
            $usuariosJson[] = [
                "id" => $usuario->getId(),
                "correo" => $usuario->getCorreo(),
                "nombre" => $usuario->getNombre(),
                "contrasena" => $usuario->getContrasena(),
                "tipo" => $usuario->getTipo()
            ];
        }
        return $this->json($usuariosJson, 200);
    }

}
