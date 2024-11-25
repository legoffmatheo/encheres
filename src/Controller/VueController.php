<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VueController extends AbstractController
{
    #[Route('/', name: 'app_vue')]
    public function index(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'VueController',
        ]);
    }

    
}
