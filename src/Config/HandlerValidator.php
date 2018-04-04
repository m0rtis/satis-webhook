<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Config;


use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouterInterface;

final class HandlerValidator extends BaseConfigValidator
{
    private $requiredKeys = [
        ContainerInterface::class => [
            BaseConfigValidator::ARRAY,
            ContainerInterface::class
        ],
        RouterInterface::class => BaseConfigValidator::ARRAY,
        'secret' => BaseConfigValidator::STRING,
        'uri_key' => BaseConfigValidator::STRING,
        'satis_config' => BaseConfigValidator::STRING,
        'output_dir' => BaseConfigValidator::STRING
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