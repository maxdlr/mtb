<?php

namespace App\Controller;

use App\Entity\Report;
use App\Form\ReportType;
use App\Repository\PostRepository;
use App\Repository\ResolutionRepository;
use App\Repository\UserRepository;
use App\Repository\ViolationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report', name: 'app_report')]
class ReportController extends AbstractController
{
    public function __construct(
        // private readonly UserRepository         $userRepository,
        // private readonly PostRepository         $postRepository,
        // private readonly EntityManagerInterface $entityManager
    )
    {
    }

    // #[Route('/new/post/{id}', name: 'new')]
    // public function index(
    //     int     $id,
    //     Request $request
    // ): Response
    // {
    //     $report = new Report();
    //     $form = $this->createForm(ReportType::class, $report);
    //     $form->handleRequest($request);
    //     $reporter = $this->userRepository->findOneByUsername($this->getUser()?->getUserIdentifier());
    //     $post = $this->postRepository->findOneBy(['id' => $id]);
    //
    //     if (!isset($reporter)) {
    //         $this->addFlash('danger', 'Tu dois te connecter pour signaler un post.');
    //         return $this->redirectToRoute('app_login', ['index' => true]);
    //     }
    //
    //     if ($form->isSubmitted() && $form->isValid()) {
    //
    //         $report
    //             ->setReportedOn(new \DateTimeImmutable())
    //             ->setReporter($reporter)
    //             ->setPost($post);
    //
    //         $this->entityManager->persist($report);
    //         $this->entityManager->flush();
    //
    //         $this->addFlash('success', 'Merci pour votre signalement !');
    //         return $this->redirectToRoute('app_redirect_user_fallback');
    //     }
    //
    //     return $this->render('report/index.html.twig', [
    //         'form' => $form,
    //     ]);
    // }
}
