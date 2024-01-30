<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(
        private readonly AuthenticationUtils $authenticationUtils,
        private readonly UserRepository      $userRepository,
    )
    {
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(): Response
    {
        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/login-success', name: 'app_login_success')]
    public function loginSuccess(): Response
    {
        $user = $this->userRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);

        $this->addFlash('success', "T'es bien connecté, " . ucfirst($user->getUsername()) . ', le gros BG.');

        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->redirectToRoute('app_home');
        }

        return $this->redirectToRoute('app_admin');

    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
