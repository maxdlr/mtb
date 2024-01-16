<?php

namespace App\Controller;

use App\Repository\SocialLinkRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    public function __construct(private SocialLinkRepository $socialLinkRepository)
    {
    }

    #[Route('/', name: 'app_default')]
    public function default(): Response
    {
        return $this->redirectToRoute('app_home', ['year' => 2023]);
    }

    #[Route('/{year}', name: 'app_home')]
    public function index(
        int $year
    ): Response
    {
        if ($year == null) {
            return $this->redirectToRoute('app_home', ['year' => 2023]);
        }

        $socialLinks = $this->socialLinkRepository->findAll();

        return $this->render('home/index.html.twig', [
            'year' => $year,
            'socialLinks' => $socialLinks,
        ]);
    }
}
