RedisBundle
===============

# Bundle providing Redis (namespaced) for services via tags

This bundle allows separation of services with namespaces. You tag each service which use Memcached, so when the container is being compiled, each service get an instance of doctrine cache provider with corresponding namespace set. Also this bundle helps store sessions in namespaces.

Also notice, that full namespace for services would be something like "company_dev_theservice_", so you even can have multiple instances of project running on the machine at the same time.

### Configuration

```yaml
framework:
    ...
    session:
        handler_id: werkint.redis.session
werkint_memcached:
    host:   localhost
    port:   11211
    prefix: company_%kernel.environment%
    session:
        prefix: company_%kernel.environment%_sess
        expire: 3600
```

### Adding tagged services (namespace: "theservice")

```
services:
    company.service:
        class: Company\MainBundle\Service\Service
        arguments:
            ...
            - @werkint.redis.ns.theservice
        tags:
            - { name: werkint.redis.cacher, ns: theservice }
```

```php
<?php
namespace Company\MainBundle\Service;

use Doctrine\Common\Cache\CacheProvider;

class Service
{
    protected $cacher;

    public function __construct(
        CacheProvider $cacher
    ) {
        $this->cacher = $cacher;
    }

    // You now can use doctrine cache provider as usual

}
```