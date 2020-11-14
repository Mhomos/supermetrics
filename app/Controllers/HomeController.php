<?php

namespace App\Controllers;

use App\Helpers\HelperMethods;
use Symfony\Component\HttpFoundation\Response;

class HomeController
{
    /**
     * Dashboard View
     *
     * @return Response
     */
    public function index(): Response
    {
        $loader = new \Twig\Loader\FilesystemLoader('resources');
        $twig = new \Twig\Environment($loader);

        return new Response($twig->render('home.html.twig' , ['reports' => HelperMethods::config('reports')]));
    }
}
