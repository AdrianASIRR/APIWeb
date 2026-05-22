<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GeneroPeliculaController extends AbstractController
{
    #[Route('/genero/pelicula', name: 'app_genero_pelicula')]
    public function index(): Response
    {
        return $this->render('genero_pelicula/index.html.twig', [
            'controller_name' => 'GeneroPeliculaController',
        ]);
    }
}
