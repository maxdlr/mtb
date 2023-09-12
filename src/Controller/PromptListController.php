<?php

namespace App\Controller;

use App\Entity\PromptList;
use App\Form\PromptListType;
use App\Repository\PromptListRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/prompt/list')]
class PromptListController extends AbstractController
{
    #[Route('/', name: 'app_prompt_list_index', methods: ['GET'])]
    public function index(PromptListRepository $promptListRepository): Response
    {
        return $this->render('prompt_list/index.html.twig', [
            'prompt_lists' => $promptListRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_prompt_list_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $promptList = new PromptList();
        $form = $this->createForm(PromptListType::class, $promptList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($promptList);
            $entityManager->flush();

            return $this->redirectToRoute('app_prompt_list_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('prompt_list/new.html.twig', [
            'prompt_list' => $promptList,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_prompt_list_show', methods: ['GET'])]
    public function show(PromptList $promptList): Response
    {
        return $this->render('prompt_list/show.html.twig', [
            'prompt_list' => $promptList,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_prompt_list_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PromptList $promptList, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PromptListType::class, $promptList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_prompt_list_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('prompt_list/edit.html.twig', [
            'prompt_list' => $promptList,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_prompt_list_delete', methods: ['POST'])]
    public function delete(Request $request, PromptList $promptList, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$promptList->getId(), $request->request->get('_token'))) {
            $entityManager->remove($promptList);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_prompt_list_index', [], Response::HTTP_SEE_OTHER);
    }
}
