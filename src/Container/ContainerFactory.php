<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Container;


final class ContainerFactory
{
    public function __invoke(iterable $config = []): Container
    {
        $definitions = $config['definitions'] ?? [];
        return new Container([], $definitions);
    }
}