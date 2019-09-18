<?php

namespace App\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use App\Entity\Image;
use App\Service\ImageResizer;

class ImageRendererExtension extends AbstractExtension
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }


    public function getFilters()
    {
        return [
            new TwigFilter('getImageUrl', [$this, 'getImageUrl']),
            new TwigFilter('getImageSrcset', [$this, 'getImageSrcset']),
        ];
    }

    public function getImageUrl(Image $image, $size = null)
    {
        return $this->router->generate('image.serve', [
            'id' => $image->getId() . (($size) ? '--' . $size : ''),
        ], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function getImageSrcset(Image $image)
    {
        $sizes = [
            ImageResizer::SIZE_1120,
            ImageResizer::SIZE_720,
            ImageResizer::SIZE_400,
            ImageResizer::SIZE_250,
        ];

        $string = '';
        foreach ($sizes as $size) {
            $string .= $this->router->generate('image.serve', [
                    'id' => $image->getId() . '--' . $size,
                ], UrlGeneratorInterface::ABSOLUTE_URL) . ' ' . $size . 'w, ';
        }
        $string = trim($string, ', ');

        return html_entity_decode($string);
    }
    
}