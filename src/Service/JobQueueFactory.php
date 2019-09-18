<?php

namespace App\Service;

use Pheanstalk\Pheanstalk;

class JobQueueFactory
{
    private $host = 'localhost';
    private $port = '11300';

    const QUEUE_IMAGE_RESIZE = 'resize';

    public function createQueue(): Pheanstalk
    {
        return Pheanstalk::create($this->host, $this->port);
    }
}
