<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostManager extends AbstractController
{
    public function new(
        Request                $request,
        EntityManagerInterface $entityManager,
        SluggerInterface       $slugger,
        User                   $owner
    ): FormInterface
    {
        $post = new Post();
        $now = new DateTimeImmutable();

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $postFile */

            $postFile = $form->get('post')->getData();

            if ($postFile) {
                $originalFilename = pathinfo($postFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $postFile->guessExtension();

                // Move the file to the directory where imgs are stored
                try {
                    $postFile->move(
                        $this->getParameter('posts_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', $e->getCode());
                    $this->addFlash('danger', $e->getMessage());
                    // ... handle exception if something happens during file upload
                }

                $post->addUser($owner);
                $post->setUploadedOn($now);
                $post->setPrompt($form->get('prompt')->getData());
                $post->setFileName($newFilename);

                $entityManager->persist($post);
            }
        }
        return $form;
    }

    public function createEditPostsForms(
        Collection             $posts,
        Request                $request,
        EntityManagerInterface $entityManager,
        FormFactoryInterface   $formFactory,
        SluggerInterface       $slugger,
        User                   $owner,
    ): array
    {
        $formViews = [];
        $submittedForms = [];

        foreach ($posts as $post) {
            $form = $formFactory->createNamed('post_' . $post->getId(), PostType::class, $post);
            $form->handleRequest($request);
            $formViews[] = $form->createView();
            $now = new DateTimeImmutable();

            if ($form->isSubmitted() && $form->isValid()) {

                // /** @var UploadedFile $imgFile */

                $postFile = $form->get('post')->getData();

                if ($postFile) {
                    $originalFilename = pathinfo($postFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $postFile->guessExtension();

                    // Move the file to the directory where imgs are stored
                    try {
                        $postFile->move(
                            $this->getParameter('posts_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    // updates the 'imgFilename' property to store the PDF file name
                    // instead of its contents
                    $singlePost = $post;

                    $singlePost->setUploadedOn($now);
                    $singlePost->setPrompt($form->get('prompt')->getData());
                    $singlePost->addUser($owner);
                    $singlePost->setFileName($newFilename);
                    $entityManager->persist($singlePost);
                }

                $entityManager->persist($post);
                $submittedForms[] = $form;
            }

        }
        return [
            'formViews' => $formViews,
            'persistedForms' => $submittedForms
        ];
    }

    public function extractFromEditPostsForms(
        array  $persistedForms,
        string $key
    ): array
    {
        $forms = [];
        foreach ($persistedForms[$key] as $form) {
            $forms[] = $form;
        }
        return $forms;
    }

    public function flushEditedPosts(
        array                  $persistedForms,
        EntityManagerInterface $entityManager,
    ): bool
    {
        foreach ($persistedForms as $form) {
            if ($form) {
                $entityManager->flush();
                $this->addFlash('success', 'Post modifié !');
                return true;
            } else {
                $this->addFlash('danger', 'Aucun post modifié');
            }
        }
        return false;
    }
}
