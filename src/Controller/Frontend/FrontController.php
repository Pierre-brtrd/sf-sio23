<?php

namespace App\Controller\Frontend;

use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FrontController extends AbstractController
{
    public function __construct(
        private readonly ArticleRepository $repoArticle
    ) {
    }

    #[Route('/', name: 'app.front.home', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('Frontend/Home/index.html.twig', [
            'articles' => $this->repoArticle->findEnableOrderByDate(3),
        ]);
    }
}
