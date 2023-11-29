<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/redirect', name: 'app_redirect_')]
class RedirectController extends AbstractController
{
    #[Route('/userFallBack', name: 'user_fallback')]
    public function verifyUserCredentials(): RedirectResponse
    {
        if ($this->getUser()) {
            return $this->redirectToRoute(
                'app_user_page', [
                    'username' => $this->getUser()->getUserIdentifier()
                ]
            );
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route('/referer', name: 'referer')]
    public function referer(Request $request): RedirectResponse
    {
        return $this->redirect($request->headers->get('referer'));
    }
}