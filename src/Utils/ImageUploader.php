<?php

namespace App\Utils;

use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageUploader
{
    public function __construct(
        #[Autowire('%cars_directory%')] private readonly string $targetDirectory,
        private readonly SluggerInterface $slugger,
    ) {
    }

    public function upload(string $filePath): string
    {
        $fileSystem = new Filesystem();
        $fileName = substr(strstr($filePath, 'Images/'), strlen('Images/'));

        try {
            $fileSystem->copy($filePath, $this->getTargetDirectory().'/'.$fileName);
        } catch (FileException $e) {
            throw new InternalErrorException();
        }

        return $fileName;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
