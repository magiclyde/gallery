<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GalleryController extends AbstractController
{   
    /** @var EntityManagerInterface */
    private $em;

	/** @var  UserManager */
    private $userManager;

	public function __construct(EntityManagerInterface $em,
        UserManager $userManager)
    {
        $this->em = $em;
        $this->userManager = $userManager;
    }

    /**
     * @Route("/gallery/{id}", name="gallery.single-gallery")
     */
    public function index($id)
    {
        $gallery = $this->em->getRepository(Gallery::class)->find($id);
        if (empty($gallery)) {
            throw new NotFoundHttpException();
        }

        $canEdit = false;
        $currentUser = $this->userManager->getCurrentUser();
        if (!empty($currentUser)) {
            $canEdit = $gallery->isOwner($currentUser);
        }
        
        return $this->render('gallery/single-gallery.html.twig', [
            'gallery' => $gallery,
            'canEdit' => $canEdit,
        ]);
    }
}
