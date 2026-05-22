<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DirectorPeliculaController extends AbstractController
{
    #[Route('/director/pelicula', name: 'app_director_pelicula')]
    public function index(): Response
    {
        return $this->render('director_pelicula/index.html.twig', [
            'controller_name' => 'DirectorPeliculaController',
        ]);
    }
}
