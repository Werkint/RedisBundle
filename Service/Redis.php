<?php
namespace Werkint\Bundle\RedisBundle\Service;

use Predis\Client;

class Redis extends Client
{
    public function __construct(
        $host,
        $port,
        $pass
    ) {
        parent::__construct([
            'host'     => $host,
            'port'     => $port,
            'password' => $pass,
        ]);
    }
}