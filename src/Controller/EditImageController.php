<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Image;
use App\Form\EditImageType;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;


class EditImageController extends AbstractController
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
     * @Route("/image/{id}/delete", name="image.delete")
     */
    public function deleteImage(Request $request, $id)
    {
        $image = $this->em->getRepository(Image::class)->find($id);
        if (empty($image)) {
            throw new NotFoundHttpException('Image not found');
        }

        $currentUser = $this->userManager->getCurrentUser();
        if (empty($currentUser) || false === $image->canEdit($currentUser)) {
            throw new AccessDeniedHttpException();
        }

        /** @var Gallery $gallery */
        $gallery = $image->getGallery();
        $this->em->remove($image);
        $this->em->flush();

        $this->addFlash('success', 'Image deleted');

        return new RedirectResponse($this->generateUrl('gallery.single-gallery', ['id' => $gallery->getId()]));
    }

    /**
     * @Route("/image/{id}/edit", name="image.edit")
     */
    public function editImage(Request $request, $id)
    {
        $image = $this->em->getRepository(Image::class)->find($id);
        if (empty($image)) {
            throw new NotFoundHttpException('Image not found');
        }

        $currentUser = $this->userManager->getCurrentUser();
        if (empty($currentUser) || false === $image->canEdit($currentUser)) {
            throw new AccessDeniedHttpException();
        }

        $imageDto = [
            'originalFilename' => $image->getOriginalFilename(),
            'description'      => $image->getDescription(),
        ];

        $form = $this->createForm(EditImageType::class, $imageDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image->setDescription($form->get('description')->getData());
            $image->setOriginalFilename($form->get('originalFilename')->getData());
            $this->em->flush();

            $this->addFlash('success', 'Image updated');

            return new RedirectResponse($this->generateUrl('image.edit', ['id' => $image->getId()]));
        }

        return $this->render('image/edit-image.html.twig', [
            'image' => $image,
            'form'  => $form->createView(),
        ]);

    }
}
