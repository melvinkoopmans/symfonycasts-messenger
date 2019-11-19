<?php

namespace App\Message\Command;

use App\Entity\ImagePost;

class DeleteImagePost
{
    /**
     * @var ImagePost
     */
    private $imagePost;

    public function __construct(ImagePost $imagePost)
    {
        $this->imagePost = $imagePost;
    }

    public function getImagePost(): ImagePost
    {
        return $this->imagePost;
    }
}