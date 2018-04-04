<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Config;


use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouterInterface;

final class Handler extends BaseConfig
{
    private $requiredKeys = [
        ContainerInterface::class,
        RouterInterface::class
    ];

    /**
     * @param iterable $config
     * @return iterable
     * @throws \InvalidArgumentException
     */
    protected function validate(iterable $config): iterable
    {
        $this->check($config, $this->requiredKeys);
        return $config;
    }
}