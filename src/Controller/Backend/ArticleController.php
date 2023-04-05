<?php

namespace App\Controller\Backend;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Search\SearchData;
use App\Form\SearchArticleType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/article')]
class ArticleController extends AbstractController
{
    public function __construct(
        private readonly ArticleRepository $repo,
        private readonly CommentRepository $commentRepo,
    ) {
    }

    #[Route('', name: 'admin.article.index', methods: ['GET'])]
    public function index(Request $request): Response|JsonResponse
    {
        $data = (new SearchData())
            ->setPage($request->get('page', 1));

        $form = $this->createForm(SearchArticleType::class, $data);
        $form->handleRequest($request);

        $articles = $this->repo->findSearchData($data, false);

        /* On vérifie si nous sommes dans le cadre d'une requête ajax */
        if ($request->get('ajax')) {
            return new JsonResponse([
                'content' => $this->renderView('Components/_articles.html.twig', [
                    'articles' => $articles,
                ]),
                'sortable' => $this->renderView('Components/_sortable.html.twig', [
                    'articles' => $articles,
                ]),
                'count' => $this->renderView('Components/_count.html.twig', [
                    'articles' => $articles,
                ]),
                'pagination' => $this->renderView('Components/_pagination.html.twig', [
                    'articles' => $articles,
                ]),
                'pages' => ceil($articles->getTotalItemCount() / $articles->getItemNumberPerPage()),
            ]);
        }

        return $this->render('Backend/Article/index.html.twig', [
            'articles' => $articles,
            'form' => $form,
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

    #[Route('/{id}/comments', name: "admin.comment.index", methods: ['POST', 'GET'])]
    public function comments(?Article $article): Response|RedirectResponse
    {
        if (!$article instanceof Article) {
            $this->addFlash('error', 'Article non trouvé');

            return $this->redirectToRoute('admin.article.index');
        }

        return $this->render('Backend/Article/Comments/index.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/comment/{id}/delete', name: "admin.comment.delete", methods: ['POST', 'DELETE'])]
    public function deleteComment(?Comment $comment, Request $req): RedirectResponse
    {
        if (!$comment instanceof Comment) {
            $this->addFlash('error', 'Commentaire non trouvé');

            return $this->redirectToRoute('admin.article.index');
        }

        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $req->get('_token'))) {
            $this->commentRepo->remove($comment, true);

            $this->addFlash('success', 'Commentaire supprimé avec succès');

            return $this->redirectToRoute('admin.comment.index', [
                'id' => $comment->getArticle()->getId(),
            ]);
        }

        $this->addFlash('error', 'Une erreur est survenue, veuillez réessayer');

        return $this->redirectToRoute('admin.comment.index', [
            'id' => $comment->getArticle()->getId(),
        ]);
    }

    #[Route('/comments/{id}/switch', name: 'admin.comment.switch', methods: ['GET'])]
    public function switchVisibility(?Comment $comment): Response
    {
        if (!$comment instanceof Comment) {
            return new Response('ERREUR', 404);
        }
        $comment->setEnabled(!$comment->isEnabled());
        $this->commentRepo->save($comment, true);

        return new Response('Visibility changed', 201);
    }
}
