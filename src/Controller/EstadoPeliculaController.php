<?php

namespace App\Controller;

use App\Entity\EstadoPelicula;
use App\Entity\Estado;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EstadoPeliculaRepository;
use App\Repository\EstadoRepository;
use App\Repository\PeliculaRepository;
use App\Repository\UsuarioRepository;
use Doctrine\Common\Collections\Criteria;

#[Route('/estado-pelicula', name: 'app_estado_pelicula')]
final class EstadoPeliculaController extends AbstractController
{
    //Crear estado pelicula
    //POST 127.0.0.1:8000/estado-pelicula/
    #[Route('/', name: 'app_estado_pelicula_crear', methods: ['POST'])]
    public function crear(
        request $request,
        EntityManagerInterface $emi,
        PeliculaRepository $peliculaRepository,
        UsuarioRepository $usuarioRepository,
        EstadoRepository $estadoRepository
    ): Response {
        // 1. Decodificar los datos JSON recibidos
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['pelicula'], $data['usuario'], $data['estado'])) {
            return $this->json('Faltan parámetros obligatorios.', 400);
        }

        // 2. Buscar las entidades relacionadas
        $pelicula = $peliculaRepository->find($data['pelicula']);
        $usuario = $usuarioRepository->find($data['usuario']);
        $estado = $estadoRepository->find($data['estado']);

        // 3. Opcional: Verificar si ya existe este estado para evitar duplicar la clave compuesta
        $existe = $emi->getRepository(EstadoPelicula::class)->findOneBy([
            'pelicula' => $pelicula,
            'usuario' => $usuario
        ]);
        if ($existe) {
            return $this->json('El estado de la película ya existe para este usuario.', 409);
        }

        // 4. Crear y configurar la nueva entidad EstadoPelicula
        $estadoPelicula = new EstadoPelicula();
        $estadoPelicula->setPelicula($pelicula);
        $estadoPelicula->setUsuario($usuario);
        $estadoPelicula->setEstado($estado);

        // Parámetros opcionales
        if (isset($data['puntuacion'])) {
            $estadoPelicula->setPuntuacion($data['puntuacion']);
        }
        if (isset($data['comentario'])) {
            $estadoPelicula->setComentario($data['comentario']);
        }
        if (isset($data['borrado'])) {
            $estadoPelicula->setBorrado((bool)$data['borrado']);
        }

        $emi->persist($estadoPelicula);
        $emi->flush();

        return $this->json("Estado de pelicula creado", 201);
    }

    //  Buscar por id compuesta
    //  GET 127.0.0.1:8000/estado-pelicula/concreta/4/12 (Pelicula 4, Usuario 12)
    #[Route('/concreta/{peliculaId}/{usuarioId}', name: 'app_estado_pelicula_concreta', methods: ['GET'])]
    public function getEstadoPelicula(int $peliculaId, int $usuarioId, EstadoPeliculaRepository $repository): Response
    {
        // Buscamos empleando la clave compuesta como un array asociativo
        $estadoPelicula = $repository->find(['pelicula' => $peliculaId, 'usuario' => $usuarioId]);

        // Verificamos si existe y si no está marcado como borrado lógico
        if (!$estadoPelicula || $estadoPelicula->isBorrado()) {
            return $this->json("No hay estadoPelicula", 404);
        }
        //Devolverá  un solo elemento
        $estadoPeliculaJson = [
            "id_compuesto" => $estadoPelicula->getCompoundId(),
            "pelicula_id" => [
                'id' => $estadoPelicula->getPelicula()->getId(),
                'titulo' => $estadoPelicula->getPelicula()->getTitulo()
            ],
            "usuario" =>  $estadoPelicula->getUsuario()->getId(),
            "estado" => [
                'id' => $estadoPelicula->getEstado()->getId(),
                'nombre' => $estadoPelicula->getEstado()->getNombre()
            ],
            'puntuacion' => $estadoPelicula->getPuntuacion(),
            'comentario' => $estadoPelicula->getComentario(),
            'borrado' => $estadoPelicula->isBorrado()
        ];

        return $this->json($estadoPeliculaJson, 200);
    }

    //  Buscar por id usuario
    //  GET 127.0.0.1:8000/estado-pelicula/usuario/5 
    #[Route('/usuario/{usuarioId}', name: 'app_estado_pelicula_usuario', methods: ['GET'])]
    public function getEstadosUsuario(int $usuarioId, EstadoPeliculaRepository $repository): Response
    {
        // Buscamos empleando la clave compuesta como un array asociativo
        $estadoPeliculas = $repository->findBy(['usuario' => $usuarioId, 'borrado' => false]);

        // Verificamos si existe y si no está marcado como borrado lógico
        if (!$estadoPeliculas) {
            return $this->json("No hay estadoPelicula", 404);
        }
        $estadoPeliculasJson = array();
        foreach ($estadoPeliculas as $estadoPelicula) {
            $estadoPeliculasJson[] = [
                "id_compuesto" => $estadoPelicula->getCompoundId(),
                "pelicula_id" => [
                    'id' => $estadoPelicula->getPelicula()->getId(),
                    'titulo' => $estadoPelicula->getPelicula()->getTitulo()
                ],
                "usuario" =>  $estadoPelicula->getUsuario()->getId(),
                "estado" => [
                    'id' => $estadoPelicula->getEstado()->getId(),
                    'nombre' => $estadoPelicula->getEstado()->getNombre()
                ],
                'puntuacion' => $estadoPelicula->getPuntuacion(),
                'comentario' => $estadoPelicula->getComentario(),
                'borrado' => $estadoPelicula->isBorrado()
            ];
        }

        return $this->json($estadoPeliculasJson, 200);
    }

    //  Buscar por id pelicula 
    //  GET 127.0.0.1:8000/estado-pelicula/pelicula/5
    #[Route('/pelicula/{peliculaId}', name: 'app_estado_pelicula_peli', methods: ['GET'])]
    public function getEstadosPelicula(int $peliculaId, EstadoPeliculaRepository $repository): Response
    {
        // Buscamos empleando la clave compuesta como un array asociativo
        $estadoPeliculas = $repository->findBy(['pelicula' => $peliculaId, 'borrado' => false]);

        // Verificamos si existe y si no está marcado como borrado lógico
        if (!$estadoPeliculas) {
            return $this->json("No hay estadoPelicula", 404);
        }

        $estadoPeliculasJson = array();
        foreach ($estadoPeliculas as $estadoPelicula) {
            $estadoPeliculasJson[] = [
                "id_compuesto" => $estadoPelicula->getCompoundId(),
                "pelicula_id" => [
                    'id' => $estadoPelicula->getPelicula()->getId(),
                    'titulo' => $estadoPelicula->getPelicula()->getTitulo()
                ],
                "usuario" =>  $estadoPelicula->getUsuario()->getId(),
                "estado" => [
                    'id' => $estadoPelicula->getEstado()->getId(),
                    'nombre' => $estadoPelicula->getEstado()->getNombre()
                ],
                'puntuacion' => $estadoPelicula->getPuntuacion(),
                'comentario' => $estadoPelicula->getComentario(),
                'borrado' => $estadoPelicula->isBorrado()
            ];
        }

        return $this->json($estadoPeliculasJson, 200);
    }

    //  Buscar por id pelicula para obtener puntuacion media
    //  GET 127.0.0.1:8000/estado-pelicula/pelicula/5/puntuacion
    #[Route('/pelicula/{peliculaId}/puntuacion', name: 'app_estado_pelicula_puntuacion', methods: ['GET'])]
    public function getPeliculaPuntuacion(int $peliculaId, EstadoPeliculaRepository $repository): Response
    {
        // Buscamos empleando la clave compuesta como un array asociativo
        // fetch all non-deleted entries for the pelicula, filter puntuacion != null in PHP
        $estadoPeliculas = $repository->findBy(['pelicula' => $peliculaId, 'borrado' => false]);

        // Verificamos si existe y si no está marcado como borrado lógico
        if (!$estadoPeliculas) {
            return $this->json("No hay estadoPelicula", 404);
        }
        // $estadoPeliculasJson = array();
        $puntuacionTotal = 0;
        $totalResenas = 0;
        foreach ($estadoPeliculas as $estadoPelicula) {
            $p = $estadoPelicula->getPuntuacion();
            if ($p !== null) {
                $puntuacionTotal += $p;
                $totalResenas++;
            }
        }

        $promedio = $totalResenas > 0 ? round($puntuacionTotal / $totalResenas, 2) : 0;

        return $this->json($promedio, 200);
    }

    //  Modificar por id compuesta
    //  PUT 127.0.0.1:8000/estado-pelicula/4/12 (Pelicula 4, Usuario 12)
    #[Route('/{peliculaId}/{usuarioId}', name: 'app_estado_pelicula_modificar', methods: ['PUT'])]
    public function modificar(int $peliculaId, int $usuarioId, EstadoPeliculaRepository $repository, EntityManagerInterface $emi, Request $request,): Response
    {
        $estadoPelicula = $emi->getRepository(EstadoPelicula::class)->find(['pelicula' => $peliculaId, 'usuario' => $usuarioId]);

        if (!$estadoPelicula || $estadoPelicula->isBorrado()) {
            return $this->json("No hay estadoPelicula", 404);
        }

        $data = json_decode($request->getContent(), true);

        if (array_key_exists('estado', $data)) {
            $estadoId = $data['estado'];

            if ($estadoId === null) {
                return $this->json('El estado de la película no puede ser nulo.', 400);
            }

            // Buscamos la entidad Estado por su ID
            $nuevoEstado = $emi->getRepository(Estado::class)->find($estadoId);

            if (!$nuevoEstado) {
                return $this->json('El estado especificado no existe.', 404);
            }

            $estadoPelicula->setEstado($nuevoEstado);
        }

        if (array_key_exists("puntuacion", $data)) {
            if ($data["puntuacion"] >= 0 && $data["puntuacion"] <= 10) {
                $valor = $data["puntuacion"];
            } elseif ($data["puntuacion"] === null) {
                $valor = null;
            }
            $estadoPelicula->setPuntuacion($valor);
        }

        if (array_key_exists("comentario", $data)) {
            if ($data["comentario"] === null) {
                $valor = null;
            } else {
                $valor = $data["comentario"];
            }
            $estadoPelicula->setComentario($valor);
        }

        $emi->persist($estadoPelicula);
        $emi->flush();

        return $this->json("EstadoPelicula modificado", 200);
    }


    // Obtener comentarios de una película
    //  GET 127.0.0.1:8000/estado-pelicula/pelicula/5/comentarios
    #[Route('/pelicula/{peliculaId}/comentarios', name: 'app_estado_pelicula_comentarios', methods: ['GET'])]
    public function getComentarios(int $peliculaId, EstadoPeliculaRepository $repository): Response
    {
        // Buscamos estados de película de la película dada que no estén borrados y tengan comentario
        $comentarios = $repository->createQueryBuilder('e')
            ->where('e.pelicula = :pelicula')
            ->andWhere('e.borrado = false')
            ->andWhere('e.comentario IS NOT NULL')
            ->setParameter('pelicula', $peliculaId)
            ->getQuery()
            ->getResult();

        // Verificamos si existe y si no está marcado como borrado lógico
        if (!$comentarios) {
            return $this->json("No hay comentarios", 404);
        }

        $comentariosJson = array();
        foreach ($comentarios as $comentario) {
            $comentariosJson[] = [
                // "id_compuesto" => $comentario->getCompoundId(),
                // "pelicula_id" => [
                //     'id' => $comentario->getPelicula()->getId(),
                //     'titulo' => $comentario->getPelicula()->getTitulo()
                // ],
                "usuario" =>  $comentario->getUsuario()->getId(),
                "nombre_usuario" => $comentario->getUsuario()->getNombre(),
                // "estado" => [
                //     'id' => $comentario->getEstado()->getId(),
                //     'nombre' => $comentario->getEstado()->getNombre()
                // ],
                'puntuacion' => $comentario->getPuntuacion(),
                'comentario' => $comentario->getComentario(),
                // 'borrado' => $comentario->isBorrado()
            ];
        }

        return $this->json($comentariosJson, 200);
    }

    //GET 127.0.0.1:8000/estado/pelicula
    /*     #[Route('/', name: 'app_estado_pelicula_list', methods: ['GET'])]
    public function list(EntityManagerInterface $eni): Response
    {
        $estadoPeliculas = $eni->getRepository(EstadoPelicula::class)->findAll();
        if (empty($estadoPeliculas)) {
            return $this->json("No hay estados de peliculas", 404);
        }
        $estadosPeliculasJson = array();
        foreach ($estadoPeliculas as $estadoPelicula) {
            $estadosPeliculasJson[] = [
                "pelicula" => $estadoPelicula->getPelicula(),
                "usuario" => $estadoPelicula->getUsuario(),
                "estado" => $estadoPelicula->getEstado(),
                "puntuacion" => $estadoPelicula->getPuntuacion(),
                "comentario" => $estadoPelicula->getComentario(),
                "borrado" => $estadoPelicula->isBorrado(),
            ];
        }
        return $this->json($estadosPeliculasJson, 200);
    }

    //Buscar estado de pelicula
    //GET 127.0.0.1:8000/estado/pelicula/3
    #[Route('/{idEstado}/{idPelicula}', name: 'app_estado_pelicula_id', methods: ['GET'])]
    public function estadoPeliculaIdComp(int $idEstado, int $idPelicula, EntityManagerInterface $eni): Response
    {
        $estadoPeliculas = $eni->getRepository(EstadoPelicula::class)->find([$idEstado, $idPelicula]);

        if (empty($estadoPeliculas)) {
            return $this->json("No hay estados de peliculas", 404);
        }
        //Devolverá  un solo elemento
        $estadosPeliculasJson = [
            "pelicula" => $estadoPeliculas->getPelicula(),
            "usuario" => $estadoPeliculas->getUsuario(),
            "estado" => $estadoPeliculas->getEstado(),
            "puntuacion" => $estadoPeliculas->getPuntuacion(),
            "comentario" => $estadoPeliculas->getComentario(),
            "borrado" => $estadoPeliculas->isBorrado(),
        ];

        return $this->json($estadosPeliculasJson, 200);
    }

    //Buscar estado de pelicula por id de pelicula
    //GET 127.0.0.1:8000/estado/pelicula/peli/3
    #[Route('/peli/{id}', name: 'app_estado_pelicula_id_pelicula', methods: ['GET'])]
    public function peliculaId(int $id, EntityManagerInterface $eni): Response
    {
        $estadoPeliculas = $eni->getRepository(EstadoPelicula::class)->find($id);

        if (empty($estadoPeliculas)) {
            return $this->json("No hay estados de peliculas", 404);
        }
        $estadosPeliculasJson = array();
        foreach ($estadoPeliculas as $estadoPelicula) {
            $estadosPeliculasJson[] = [
                "pelicula" => $estadoPelicula->getPelicula(),
                "usuario" => $estadoPelicula->getUsuario(),
                "estado" => $estadoPelicula->getEstado(),
                "puntuacion" => $estadoPelicula->getPuntuacion(),
                "comentario" => $estadoPelicula->getComentario(),
                "borrado" => $estadoPelicula->isBorrado(),
            ];
        }
        
        return $this->json($estadosPeliculasJson, 200);
    }

    //Buscar estado de pelicula por id de pelicula
    //GET 127.0.0.1:8000/estado/pelicula/3
    #[Route('/{id}', name: 'app_estado_pelicula_id_usuario', methods: ['GET'])]
    public function peliculaId(int $id, EntityManagerInterface $eni): Response
    {
        $estadoPeliculas = $eni->getRepository(EstadoPelicula::class)->find($id);

        if (empty($estadoPeliculas)) {
            return $this->json("No hay estados de peliculas", 404);
        }
        $estadosPeliculasJson = array();
        foreach ($estadoPeliculas as $estadoPelicula) {
            $estadosPeliculasJson[] = [
                "pelicula" => $estadoPelicula->getPelicula(),
                "usuario" => $estadoPelicula->getUsuario(),
                "estado" => $estadoPelicula->getEstado(),
                "puntuacion" => $estadoPelicula->getPuntuacion(),
                "comentario" => $estadoPelicula->getComentario(),
                "borrado" => $estadoPelicula->isBorrado(),
            ];
        }

        return $this->json($estadosPeliculasJson, 200);
    }


    //Crear pelicula
    //POST 127.0.0.1:8000/estado/pelicula/
    #[Route('/', name: 'app_estado_pelicula_crear', methods: ['POST'])]
    public function crear(request $request, EntityManagerInterface $eni): Response
    {
        $body = $request->getContent();
        $data = json_decode($body, true);

        if (!$data) {
            return $this->json("Error JSON no valido", 404);
        }
        if (!isset($data["pelicula"])) {
            return $this->json("No hay pelicula", 400);
        }

        if (!isset($data["usuario"])) {
            return $this->json("No hay usuario", 400);
        }
        $estadoPelicula = new EstadoPelicula();
        $estadoPelicula->setPelicula($data["pelicula"]);
        $estadoPelicula->setUsuario($data["usuario"]);

        if (isset($data["estado"])) {
            $estadoPelicula->setEstado($data["estado"]);
        }
        if (isset($data["puntuacion"])) {
            $estadoPelicula->setPuntuacion($data["puntuacion"]);
        }
        if (isset($data["comentario"])) {
            $estadoPelicula->setComentario($data["comentario"]);
        }

        $eni->persist($estadoPelicula);
        $eni->flush();

        return $this->json("Estado de pelicula creado", 201);
    }

    //Borrar estado de pelicula por id
    //POST 127.0.0.1:8000/estado/pelicula/6
    #[Route('/{id}', name: 'app_estado_pelicula_borrar', methods: ['POST'])]
    public function borrar(int $id, EntityManagerInterface $eni): Response
    {

        $estadoPeliculas = $eni->getRepository(EstadoPelicula::class)->find($id);

        if (empty($estadoPeliculas)) {
            return $this->json("No existe este estado de pelicula", 404);
        }
        //Devolverá  un solo elemento
        $eni->remove($estadoPeliculas);
        $eni->flush();
        return $this->json("Estado de pelicula borrado", 200);
    }


    //modificar estado de pelicula
    //PUT 127.0.0.1:8000/estado/pelicula/6
    #[Route('/{id}', name: 'app_estado_pelicula_modificar', methods: ['PUT'])]
    public function modificar(int $id, EntityManagerInterface $eni, Request $request): Response
    {

        $estadoPelicula = $eni->getRepository(EstadoPelicula::class)->find($id);

        if (empty($estadoPelicula)) {
            return $this->json("No existe este estado de pelicula", 404);
        }

        $data = json_decode($request->getContent(), true);


        if (isset($data["pelicula"])) {
            $estadoPelicula->setPelicula($data["pelicula"]);
        }
        if (isset($data["usuario"])) {
            $estadoPelicula->setUsuario($data["usuario"]);
        }
        if (isset($data["estado"])) {
            $estadoPelicula->setEstado($data["estado"]);
        }
        if (isset($data["puntuacion"])) {
            $estadoPelicula->setPuntuacion($data["puntuacion"]);
        }
        if (isset($data["comentario"])) {
            $estadoPelicula->setComentario($data["comentario"]);
        }

        $eni->persist($estadoPelicula);
        $eni->flush();

        return $this->json("Estado de pelicula modificado", 200);
    }

    //Borrado lógico pelicula
    //PUT 127.0.0.1:8000/estado/pelicula/blogico/4
    #[Route('/blogico/{id}', name: 'app_estado_pelicula_borrado_logico', methods: ['PUT'])]
    public function borradoLogico(int $id, EntityManagerInterface $eni, Request $request): Response
    {

        $estadoPelicula = $eni->getRepository(EstadoPelicula::class)->find($id);

        if (empty($estadoPelicula)) {
            return $this->json("No existe este estado de pelicula", 404);
        }

        $data = json_decode($request->getContent(), true);

        $estadoPelicula->setBorrado(1);

        $eni->persist($estadoPelicula);
        $eni->flush();

        return $this->json("Estado de pelicula borrado lógicamente", 200);
    } */
}
