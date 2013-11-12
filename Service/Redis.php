<?php
namespace Werkint\Bundle\RedisBundle\Service;

use Predis\Client;

/**
 * Redis.
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class Redis extends Client
{
    /**
     * @param string $host
     * @param int    $port
     * @param string $pass
     */
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
