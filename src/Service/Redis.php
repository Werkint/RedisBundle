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
     * @param array $connection
     */
    public function __construct(
        array $connection
    ) {
        parent::__construct([
            'host'     => $connection['host'],
            'port'     => $connection['port'],
            'password' => $connection['pass'],
        ]);
    }

}
