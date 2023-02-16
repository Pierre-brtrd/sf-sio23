<?php

namespace App\Controller\Frontend;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/articles')]
class ArticleController extends AbstractController
{
    public function __construct(
        private readonly ArticleRepository $repo
    ) {
    }

    #[Route('/', name: 'app.article.index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('Frontend/Article/index.html.twig', [
            'articles' => $this->repo->findEnableOrderByDate()
        ]);
    }

    #[Route('/details/{slug}', name: 'app.article.show', methods: ['GET'])]
    public function details(?Article $article): Response|RedirectResponse
    {
        if (!$article instanceof Article) {
            $this->addFlash('error', 'Article non trouvé, vérifiez votre url');

            return $this->redirectToRoute('app.article.index');
        }

        return $this->render('Frontend/Article/show.html.twig', [
            'article' => $article,
        ]);
    }
}
