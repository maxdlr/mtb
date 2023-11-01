<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploadManager
{
    public function __construct(
        private readonly SluggerInterface      $slugger,
        private readonly ParameterBagInterface $parameterBag
    )
    {
    }

    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
        //todo: validate: postfile size, owner is user, postfile extension

        try {
            $file->move(
                $this->parameterBag->get('posts_directory'),
                $newFilename
            );
        } catch (FileException $e) {
            //todo: throw errors into toasts
            dump($e);
        }

        return $newFilename;
    }
}