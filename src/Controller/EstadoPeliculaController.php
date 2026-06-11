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

        $baseUrl = 'http://127.0.0.1:8000/imagenes/pelicula/';



        $estadoPeliculasJson = array();
        foreach ($estadoPeliculas as $estadoPelicula) {
            $fotoUrl = $estadoPelicula->getPelicula()->getImagenRuta() ? $baseUrl . $estadoPelicula->getPelicula()->getImagenRuta() : $baseUrl . "placeholder.jpg";

            $estadoPeliculasJson[] = [
                "id_compuesto" => $estadoPelicula->getCompoundId(),
                "pelicula_id" => [
                    'id' => $estadoPelicula->getPelicula()->getId(),
                    'titulo' => $estadoPelicula->getPelicula()->getTitulo(),
                    'imagenRuta' => $fotoUrl
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

        // if ($totalResenas === 0) {
        //     return $this->json("No hay puntuaciones para esta película", 404);
        // }
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

    // Obtener 2 primeros comentarios de una película
    //  GET 127.0.0.1:8000/estado-pelicula/pelicula/5/comentarios/2
    #[Route('/pelicula/{peliculaId}/comentarios/{limite}', name: 'app_estado_pelicula_comentarios_limite', methods: ['GET'])]
    public function getComentariosLimite(int $peliculaId, int $limite, EstadoPeliculaRepository $repository): Response
    {
        // Buscamos estados de película de la película dada que no estén borrados y tengan comentario
        $comentarios = $repository->createQueryBuilder('e')
            ->where('e.pelicula = :pelicula')
            ->andWhere('e.borrado = false')
            ->andWhere('e.comentario IS NOT NULL')
            ->setParameter('pelicula', $peliculaId)
            ->setMaxResults($limite)
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
                'puntuacion' => $comentario->getPuntuacion(),
                'comentario' => $comentario->getComentario(),
            ];
        }

        return $this->json($comentariosJson, 200);
    }

    //Borrado Lógico
    //  PUT 127.0.0.1:8000/estado-pelicula/blogico/4/12 (Pelicula 4, Usuario 12)
    #[Route('/blogico/{peliculaId}/{usuarioId}', name: 'app_estado_pelicula_blogico', methods: ['PUT'])]
    public function borradoLogico(int $peliculaId, int $usuarioId, EntityManagerInterface $emi, Request $request,): Response
    {
        $estadoPelicula = $emi->getRepository(EstadoPelicula::class)->find(['pelicula' => $peliculaId, 'usuario' => $usuarioId]);

        if (!$estadoPelicula) {
            return $this->json("No hay estadoPelicula", 404);
        }

        $data = json_decode($request->getContent(), true);
        $estadoPelicula->setBorrado(1);


        $emi->persist($estadoPelicula);
        $emi->flush();

        return $this->json("EstadoPelicula modificado", 200);
    }

}
