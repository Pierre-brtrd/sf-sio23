<?php

namespace App\Controller\Backend;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/article')]
class ArticleController extends AbstractController
{
    public function __construct(
        private readonly ArticleRepository $repo
    ) {
    }

    #[Route('/', name: 'admin.article.index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('Backend/Article/index.html.twig', [
            'articles' => $this->repo->findAll(),
        ]);
    }

    #[Route('/create', name: 'admin.article.create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response|RedirectResponse
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setAuthor($this->getUser());
            $this->repo->save($article, true);

            $this->addFlash('success', 'Article sauvegardé avec succès');

            return $this->redirectToRoute('admin.article.index');
        }

        return $this->render('Backend/Article/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/update/{id}', name: 'admin.article.update', methods: ['GET', 'POST'])]
    public function update(Article $article, Request $request): Response|RedirectResponse
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repo->save($article, true);
            $this->addFlash('success', 'Article mis à jour avec succès');

            return $this->redirectToRoute('admin.article.index');
        }

        return $this->render('Backend/Article/update.html.twig', [
            'form' => $form,
            'article' => $article,
        ]);
    }

    #[Route('/delete/{id}', name: 'admin.article.delete', methods: ['POST', 'DELETE'])]
    public function delete(Article $article, Request $request): RedirectResponse
    {
        if (!$article instanceof Article) {
            $this->addFlash('error', 'Article non trouvé');

            return $this->redirectToRoute('admin.article.index');
        }

        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->get('_token'))) {
            $this->repo->remove($article, true);
            $this->addFlash('success', 'Article deleted successfully');

            return $this->redirectToRoute('admin.article.index');
        }

        $this->addFlash('error', 'invalid token');

        return $this->redirectToRoute('admin.article.index');
    }
}
