<?php

namespace App\Controller\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app.login')]
    public function login(AuthenticationUtils $authUtils): Response
    {
        // Get the error
        $error = $authUtils->getLastAuthenticationError();

        // Get the last username connect
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('login.html.twig', [
            'error' => $error,
            'lastUsername' => $lastUsername,
        ]);
    }
}
