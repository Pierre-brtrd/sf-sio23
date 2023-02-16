<?php

namespace App\Controller\Backend;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/categorie')]
class CategorieController extends AbstractController
{
    #[Route('/', name: 'admin.categorie.index', methods: ['GET'])]
    public function index(CategorieRepository $categorieRepository): Response
    {
        return $this->render('Backend/Categorie/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'admin.categorie.create', methods: ['GET', 'POST'])]
    public function create(Request $request, CategorieRepository $categorieRepository): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorieRepository->save($categorie, true);

            return $this->redirectToRoute('admin.categorie.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Backend/Categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    #[Route('/update/{id}', name: 'admin.categorie.update', methods: ['GET', 'POST'])]
    public function update(Request $request, Categorie $categorie, CategorieRepository $categorieRepository): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorieRepository->save($categorie, true);

            return $this->redirectToRoute('admin.categorie.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Backend/Categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'admin.categorie.delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie, CategorieRepository $categorieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $categorie->getId(), $request->request->get('_token'))) {
            $categorieRepository->remove($categorie, true);
        }

        return $this->redirectToRoute('admin.categorie.index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/switch/{id}', name: 'admin.categorie.switch', methods: ['GET'])]
    public function switchVisility(?Categorie $categorie, CategorieRepository $repo): Response
    {
        if ($categorie instanceof Categorie) {
            $categorie->setEnabled(!$categorie->isEnabled());
            $repo->save($categorie, true);

            return new Response('Visibility changed', 201);
        }

        return new Response('ERREUR', 404);
    }
}
