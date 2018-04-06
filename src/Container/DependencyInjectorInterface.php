<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Container;


use Psr\Container\ContainerInterface;

interface DependencyInjectorInterface
{
    public function __construct(ContainerInterface $container);

    public function canInstantiate(string $className): bool;

    public function instantiate(string $className): object;
}