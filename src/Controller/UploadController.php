<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Image;
use App\Event\GalleryCreatedEvent;
use App\Service\FileManager;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


class UploadController extends AbstractController
{
    /** @var  EntityManagerInterface */
    private $em;

    /** @var FileManager */
    private $fileManager;

    /** @var  UserManager */
    private $userManager;

    /** @var  EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        EntityManagerInterface $em,
        FileManager $fileManager,
        UserManager $userManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->em = $em;
        $this->fileManager = $fileManager;
        $this->userManager = $userManager;
        $this->eventDispatcher = $eventDispatcher;
    }

	/**
     * @Route("/private/upload", name="upload")
     */
    public function renderUploadScreen(Request $request)
    {
        return $this->render('gallery/upload.html.twig');
    }
    
    /**
     * @Route("/private/upload-process", name="upload.process")
     */
    public function processUpload(Request $request)
    {
        // @todo access control
        // @todo input validation

        $gallery = new Gallery(Uuid::getFactory()->uuid4());
        $gallery->setName($request->get('name', null));
        $gallery->setDescription($request->get('description', null));
        $gallery->setUser($this->userManager->getCurrentUser());
        $files = $request->files->get('file');

        $this->fileManager->setBaseUrl($request->getSchemeAndHttpHost());

        /** @var UploadedFile $file */
        foreach ($files as $file) {
            $filename = $file->getClientOriginalName();
            $filepath = Uuid::getFactory()->uuid4()->toString() . '.' . $file->getClientOriginalExtension();
            $this->fileManager->upload($file, $filepath);

            $image = new Image(
                Uuid::getFactory()->uuid4(),
                $filename,
                $filepath
            );

            $gallery->addImage($image);
        }

        $this->em->persist($gallery);
        $this->em->flush();

        $this->eventDispatcher->dispatch(
            GalleryCreatedEvent::class,
            new GalleryCreatedEvent($gallery->getId())
        );

        $this->addFlash('success', 'Gallery created! Images are now being processed.');

        return new JsonResponse([
            'success'     => true,
            'redirectUrl' => $this->generateUrl(
                'gallery.single-gallery', ['id' => $gallery->getId()]
            ),
        ]);

    }
}