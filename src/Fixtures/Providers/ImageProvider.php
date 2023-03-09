<?php

namespace App\Fixtures\Providers;

use App\Entity\ArticleImage;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageProvider
{
    public function uploadImageArticle(): ArticleImage
    {
        $files = glob(realpath(__DIR__ . '/Images/Articles/') . '/*.*');

        $imageFile = new File($files[array_rand($files)]);

        $uploadFile = new UploadedFile($imageFile, $imageFile->getFilename());

        $image = new ArticleImage();
        $image->setImageFile($uploadFile);

        return $image;
    }
}
