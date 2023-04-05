<?php

namespace App\Controller\Frontend;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
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

#[Route('/articles')]
class ArticleController extends AbstractController
{
    public function __construct(
        private readonly ArticleRepository $repo,
        private readonly CommentRepository $repoComment
    ) {
    }

    #[Route('', name: 'app.article.index', methods: ['GET'])]
    public function index(Request $request): Response|JsonResponse
    {
        $data = (new SearchData())
            ->setPage($request->get('page', 1));

        $form = $this->createForm(SearchArticleType::class, $data);
        $form->handleRequest($request);

        $articles = $this->repo->findSearchData($data);

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

        return $this->render('Frontend/Article/index.html.twig', [
            'articles' => $articles,
            'form' => $form,
        ]);
    }

    #[Route('/details/{slug}', name: 'app.article.show', methods: ['GET', 'POST'])]
    public function details(?Article $article, Request $request): Response|RedirectResponse
    {
        if (!$article instanceof Article) {
            $this->addFlash('error', 'Article non trouvé, vérifiez votre url');

            return $this->redirectToRoute('app.article.index');
        }

        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($this->getUser())
                ->setArticle($article)
                ->setEnabled(true);

            $this->repoComment->save($comment, true);

            $this->addFlash('success', 'Votre commentaire a été posté avec succès');

            return $this->redirectToRoute('app.article.show', [
                'slug' => $article->getSlug()
            ], 301);
        }

        return $this->render('Frontend/Article/show.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }
}
