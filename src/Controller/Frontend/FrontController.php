<?php

namespace App\Controller\Frontend;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FrontController extends AbstractController
{
    #[Route('/', name: 'app.front.home')]
    public function index(): Response
    {
        $data = ['Pierre', 'Paul', 'Jacques'];

        return $this->render('Frontend/Home/index.html.twig', [
            'data' => $data,
        ]);
    }
}
