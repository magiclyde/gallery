<?php

namespace App\Controller;

use App\Service\TusService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class TusController extends AbstractController
{
    // ...

    /**
     * Create tus server. Route matches /tus/, /tus and /tus/* endpoints.
     *
     * @Route("/tus/", name="tus_post")
     * @Route("/tus/{token?}", name="tus", requirements={"token"=".+"})
     *
     * @param TusService $tusService
     *
     * @return Response
     */
    public function server(TusService $tusService)
    {
        $response = $tusService->getServer()->serve();

        return $response->send();
    }

    // ...
}