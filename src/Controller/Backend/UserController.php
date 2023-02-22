<?php

namespace App\Controller\Backend;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/user')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    #[Route('', name: 'admin.user.index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('Backend/User/index.html.twig', [
            'users' => $this->userRepository->findAll(),
        ]);
    }

    #[Route('/update/{id}', name: 'admin.user.update', methods: ['GET', 'POST'])]
    public function update(?User $user, Request $request): Response|RedirectResponse
    {
        if (!$user instanceof User) {
            $this->addFlash('error', 'Utilisateur non trouvé, vérifiez votre url');

            return $this->redirectToRoute('admin.user.index');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->save($user, true);

            $this->addFlash('success', 'User modifié avec succès');

            return $this->redirectToRoute('admin.user.index');
        }

        return $this->render('Backend/User/update.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/delete/{id}', name: 'admin.user.delete', methods: ['POST'])]
    public function delete(?User $user, Request $request): Response|RedirectResponse
    {
        if (!$user instanceof User) {
            $this->addFlash('error', 'User non trouvé');

            return $this->redirectToRoute('admin.user.index');
        }

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->get('_token'))) {
            $this->userRepository->remove($user, true);

            $this->addFlash('success', 'User delete avec succès');

            return $this->redirectToRoute('admin.user.index');
        }

        $this->addFlash('error', 'Invalid token');

        return $this->redirectToRoute('admin.user.index');
    }
}
