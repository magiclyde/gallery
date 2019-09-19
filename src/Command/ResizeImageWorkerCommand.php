<?php

namespace App\Command;

use App\Entity\Image;
use App\Service\FileManager;
use App\Service\ImageResizer;
use App\Service\JobQueueFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResizeImageWorkerCommand extends Command
{
    /** @var  OutputInterface */
    private $output;

    /** @var  EntityManagerInterface */
    private $em;

    /** @var  JobQueueFactory */
    private $jobQueueFactory;

    /** @var  ImageResizer */
    private $imageResizer;

    /** @var  FileManager */
    private $fileManager;

    public function __construct(EntityManagerInterface $em,
        JobQueueFactory $jobQueueFactory, 
        ImageResizer $imageResizer,
        FileManager $fileManager)
    {
        $this->em = $em;
        $this->jobQueueFactory = $jobQueueFactory;
        $this->imageResizer = $imageResizer;
        $this->fileManager = $fileManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('app:resize-image-worker');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $output->writeln(sprintf('Started worker'));

        $queue = $this->jobQueueFactory
            ->createQueue()
            ->watch(JobQueueFactory::QUEUE_IMAGE_RESIZE);

        $job = $queue->reserve(60 * 5);

        if (false === $job) {
            $this->output->writeln('Timed out');

            return;
        }

        try {
            $this->resizeImage($job->getData());
            $queue->delete($job);
        } catch (\Exception $e) {
            $queue->bury($job);
            throw $e;
        }
    }

    protected function resizeImage(string $imageId)
    {
        /** @var Image $image */
        $image = $this->em->getRepository(Image::class)->find($imageId);

        if (empty($image)) {
            $this->output->writeln("Image with ID $imageId not found");
        }

        $fullPath = $this->fileManager->getFilePath($image->getFilename());
        if (empty($fullPath)) {
            $this->output->writeln("Full path for image with ID $imageId is empty");

            return;
        }

        $cachedPaths = [];
        foreach ($this->imageResizer->getSupportedWidths() as $width) {
            $cachedPaths[$width] = $this->imageResizer->getResizedPath($fullPath, $width, true);
        }

        $this->output->writeln("Thumbnails generated for image $imageId");
        $this->output->writeln(json_encode($cachedPaths, JSON_PRETTY_PRINT));
    }
}
