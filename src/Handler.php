<?php
declare(strict_types=1);

namespace Composer\Satis\Webhook;


use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Interfaces\RouterInterface;

final class Handler extends App
{
    private $requiredConfigKeys = [
        ContainerInterface::class,
        RouterInterface::class
    ];

    public function __construct(iterable $config)
    {
        $this->checkConfig($config);
        $config = $this->setDefaults($config);
        $container = $this->initContainerFromConfig($config[ContainerInterface::class]);
        parent::__construct($config, $container);
        $this->initRoutesFromConfig($config[RouterInterface::class]);
    }

    /**
     * @param iterable|ContainerInterface $containerConfig
     * @return null|ContainerInterface
     */
    private function initContainerFromConfig($containerConfig): ?ContainerInterface
    {
        if ($containerConfig instanceof ContainerInterface || null === $containerConfig) {
            return $containerConfig;
        }
        if (isset($containerConfig['factory_class']) && class_exists($containerConfig['factory_class'])) {
            $factory = new $containerConfig['factory_class'];
            return $factory($containerConfig);
        }

        return null;
    }

    /**
     * @param iterable $config
     * @throws RequiredConfigKeyDoesNotExistException
     */
    private function checkConfig(iterable $config): void
    {
        foreach ($this->requiredConfigKeys as $requiredConfigKey) {
            if (!array_key_exists($config[$requiredConfigKey], $config)) {
                throw new RequiredConfigKeyDoesNotExistException($requiredConfigKey);
            }
        }
    }

    /**
     * @param iterable $config
     * @param string $globPattern
     * @return iterable
     */
    private function setDefaults(iterable $config, string $globPattern = './Config/*.config.php'): iterable
    {
        $globPattern = (new \SplFileInfo($globPattern))->getRealPath();
        $iterator = new \GlobIterator($globPattern, GLOB_BRACE);
        $defaults = [];
        /** @var \GlobIterator $file */
        foreach ($iterator as $file) {
            $defaults[] = require $file->getPathname();
        }
        $defaults = array_merge(...$defaults);

        return array_merge_recursive($defaults, $config);
    }

    /**
     * @param iterable $config
     */
    private function initRoutesFromConfig(iterable $config): void
    {
        foreach ($config as $name => $route) {
            if ('group' === $route['type']) {
                $group = $this->group($route['pattern'], $route['handler']);
                foreach ($route['routes'] as $groupRoute) {
                    $this->map($groupRoute['methods'], $groupRoute['pattern'], $groupRoute['handler']);
                }
                continue;
            }
        }
    }
}