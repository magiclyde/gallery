<?php

namespace App\Controller;

use App\Entity\Gallery;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    const PER_PAGE = 12;

    /** @var EntityManagerInterface */
    private $em;

    /** @var  UserManager */
    private $userManager;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $galleries = $this->em->getRepository(Gallery::class)->findBy([], ['createdAt' => 'DESC'], self::PER_PAGE);

        return $this->render('home.html.twig', [
            'galleries' => $galleries,
        ]);
    }

    /**
     * @Route("/galleries-lazy-load", name="home.lazy-load")
     */
    public function homeGalleriesLazyLoad(Request $request)
    {
        $page = $request->get('page', null);
        if (empty($page)) {
            return new JsonResponse([
                'success' => false,
                'msg'     => 'Page param is required',
            ]);
        }

        $offset = ($page - 1) * self::PER_PAGE;
        $galleries = $this->getDoctrine()->getRepository(Gallery::class)->findBy([], ['createdAt' => 'DESC'], 12, $offset);

        $view = $this->render('partials/home-galleries-lazy-load.html.twig', [
            'galleries' => $galleries,
        ]);

        return new JsonResponse([
            'success' => true,
            'data'    => $view,
        ]);
    }
    
}
