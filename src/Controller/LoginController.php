<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login/{index?}', name: 'app_login')]
    public function index(
        AuthenticationUtils $authenticationUtils,
                            $index = null
    ): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername() ?? null;

        if (!is_null($index))
            return $this->render('auth/login-index.html.twig', [
                'last_username' => $lastUsername,
                'error' => $error,
            ]);

        return $this->render('auth/_login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/login-confirm', name: 'app_login_confirm')]
    public function loginConfirm(
        AuthenticationUtils $authenticationUtils
    ): RedirectResponse
    {
        $this->addFlash('success', 'Hello ' . ucfirst($authenticationUtils->getLastUsername()));
        return $this->redirectToRoute('app_redirect_referer');
    }

    #[Route('/logout-confirm', name: 'app_logout_confirm')]
    public function logoutConfirm(): RedirectResponse
    {
        $this->addFlash('success', 'Déconnexion réussie, à plus dans le bus.');
        return $this->redirectToRoute('app_redirect_referer');
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): never
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
