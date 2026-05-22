<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ActorPeliculaController extends AbstractController
{
    #[Route('/actor/pelicula', name: 'app_actor_pelicula')]
    public function index(): Response
    {
        return $this->render('actor_pelicula/index.html.twig', [
            'controller_name' => 'ActorPeliculaController',
        ]);
    }
}
