<?php

namespace App\Controller;

use App\Form\ParticipantType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormController extends AbstractController
{
    #[Route('/form/{formClassName}')]
    public function show(
        string $formClassName
    ): Response
    {
        $formClassName = match ($formClassName) {
            'participant' => ParticipantType::class
        };

        return $this->render('form/index.html.twig', [
            'formClassName' => $formClassName
        ]);
    }

}