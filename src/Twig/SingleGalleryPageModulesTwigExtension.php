<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Entity\Gallery;
use App\Repository\GalleryRepository;
use Twig_Environment;

class SingleGalleryPageModulesTwigExtension extends AbstractExtension
{
    /** @var Twig_Environment */
    private $twig;

    /** @var  GalleryRepository */
    private $repository;

    public function __construct(Twig_Environment $twig, GalleryRepository $repository)
    {
        $this->twig = $twig;
        $this->repository = $repository;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('renderRelatedGalleries', [$this, 'renderRelatedGalleries'], ['is_safe' => ['html']]),
            new TwigFunction('renderNewestGalleries', [$this, 'renderNewestGalleries'], ['is_safe' => ['html']]),
        ];
    }

    public function renderRelatedGalleries(Gallery $gallery, int $limit = 5)
    {
        return $this->twig->render('gallery/partials/_related-galleries.html.twig', [
            'galleries' => $this->repository->findRelated($gallery, $limit),
        ]);
    }

    public function renderNewestGalleries(int $limit = 5)
    {
        return $this->twig->render('gallery/partials/_newest-galleries.html.twig', [
            'galleries' => $this->repository->findNewest($limit),
        ]);
    }

}