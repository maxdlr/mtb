<?php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\SecurityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request                     $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface      $entityManager,
        Security                    $security
    ): Response
    {
        if ($this->getUser()) {
            $this->addFlash('warning', 'Déjà connecté.');
            return $this->redirectToRoute('app_redirect_user_fallback');
        }

        $user = new User();
        $page = new Page();
        $now = new \DateTimeImmutable();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            )
                ->setPage($page)
                ->setRegistrationDate($now);

            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Compte créé.');
            // do anything else you need here, like send an email
            $security->login($user, 'form_login');
            return $this->redirectToRoute('app_redirect_user_fallback');
        }

        return $this->render('auth/register-index.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
