<?php

namespace App\MessageHandler\Command;

use App\Message\Command\AddPonkaToImage;
use App\Photo\PhotoFileManager;
use App\Photo\PhotoPonkaficator;
use App\Repository\ImagePostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AddPonkaToImageHandler implements MessageHandlerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var PhotoPonkaficator
     */
    private $ponkaficator;

    /**
     * @var PhotoFileManager
     */
    private $photoManager;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ImagePostRepository
     */
    private $imagePostRepository;

    public function __construct(
        PhotoPonkaficator $ponkaficator,
        PhotoFileManager $photoManager,
        ImagePostRepository $imagePostRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->ponkaficator = $ponkaficator;
        $this->photoManager = $photoManager;
        $this->imagePostRepository = $imagePostRepository;
        $this->entityManager = $entityManager;
    }

    public function __invoke(AddPonkaToImage $addPonkaToImage)
    {
        $imagePostId = $addPonkaToImage->getImagePostId();
        $imagePost = $this->imagePostRepository->find($imagePostId);

        if (!$imagePost) {
            $this->logger->alert(sprintf('Image post %d was missing', $imagePostId));
            return;
        }

        //if (rand(0, 10) < 7) {
        //    throw new \Exception('I failed randomly.');
        //}

        $updatedContents = $this->ponkaficator->ponkafy(
            $this->photoManager->read($imagePost->getFilename())
        );
        $this->photoManager->update($imagePost->getFilename(), $updatedContents);
        $imagePost->markAsPonkaAdded();

        $this->entityManager->persist($imagePost);
        $this->entityManager->flush();
    }
}