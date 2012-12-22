<?php
namespace Werkint\Bundle\MemcachedBundle\Service;

use \Memcached as Ref;

class Memcached extends Ref
{
    public function __construct($host, $port)
    {
        parent::__construct();
        $this->addServer($host, $port);
    }
}