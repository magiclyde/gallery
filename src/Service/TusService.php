<?php

namespace App\Service;

use TusPhp\Tus\Server as TusServer;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TusService
{
    /** @var ParameterBagInterface */
    protected $params;

    /** @var TusPhp\Tus\Server */
    protected $server;

    /**
     * TusService constructor.
     *
     * @param ParameterBagInterface $params
     */
    public function __construct(ParameterBagInterface $params, TusServer $server)
    {
        $this->params = $params;
        $this->server = $server;
    }

    /**
     * Configure and get TusServer instance.
     *
     * @return TusServer
     */
    public function getServer()
    {
        $this->server
            ->setApiPath('/tus') // tus server endpoint.
            ->setUploadDir($this->params->get('kernel.project_dir') . '/var/uploads'); // uploads dir.

        return $this->server;
    }
}