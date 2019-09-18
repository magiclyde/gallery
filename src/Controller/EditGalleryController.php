<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Image;
use App\Form\EditGalleryType;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class EditGalleryController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var  UserManager */
    private $userManager;

    public function __construct(
        EntityManagerInterface $em,
        UserManager $userManager)
    {
        $this->em = $em;
        $this->userManager = $userManager;
    }

    /**
     * @Route("/gallery/{id}/delete", name="gallery.delete")
     */
    public function deleteGallery(Request $request, $id)
    {
       $gallery = $this->em->getRepository(Gallery::class)->find($id);
        if (empty($gallery)) {
            throw new NotFoundHttpException('Gallery not found');
        }

        $currentUser = $this->userManager->getCurrentUser();
        if (empty($currentUser) || false === $gallery->isOwner($currentUser)) {
            throw new AccessDeniedHttpException();
        }

        $this->em->remove($gallery);
        $this->em->flush();

        $this->addFlash('success', 'Gallery deleted');

        return new RedirectResponse($this->generateUrl('home'));
    }

    /**
     * @Route("/gallery/{id}/edit", name="gallery.edit")
     */
    public function editGallery(Request $request, $id)
    {
        $gallery = $this->em->getRepository(Gallery::class)->find($id);
        if (empty($gallery)) {
            throw new NotFoundHttpException('Gallery not found');
        }

        $currentUser = $this->userManager->getCurrentUser();
        if (empty($currentUser) || false === $gallery->isOwner($currentUser)) {
            throw new AccessDeniedHttpException();
        }

        $galleryDto = [
            'name'        => $gallery->getName(),
            'description' => $gallery->getDescription(),
        ];

        $form = $this->createForm(EditGalleryType::class, $galleryDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $gallery->setDescription($form->get('description')->getData());
            $gallery->setName($form->get('name')->getData());
            $this->em->flush();

            $this->addFlash('success', 'Gallery updated');

            return new RedirectResponse($this->generateUrl('gallery.edit', ['id' => $gallery->getId()]));
        }

        return $this->render('gallery/edit-gallery.html.twig', [
            'gallery' => $gallery,
            'form'    => $form->createView(),
        ]);

    }
}
