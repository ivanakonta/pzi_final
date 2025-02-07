<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WelComeController extends AbstractController
{
    #[Route('/', name: 'welcome')]
    public function index(): Response
    {
        return $this->render("home.html.twig");
    }
}
