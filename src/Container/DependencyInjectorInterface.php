<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Container;


use Psr\Container\ContainerInterface;

interface DependencyInjectorInterface
{
    public function __construct(ContainerInterface $container);

    public function canInstantiate(string $id): bool;

    public function instantiate(string $id): object;
}