<?php
declare(strict_types=1);

namespace Composer\Satis\Webhook;


use Composer\Satis\Webhook\Config\Handler as HandlerConfigValidator;
use Composer\Satis\Webhook\Config\Routes;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Interfaces\RouterInterface;

final class Handler extends App
{
    /**
     * Handler constructor.
     * @param iterable $config
     */
    public function __construct(iterable $config)
    {
        $config = new HandlerConfigValidator($config);
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
        $config = new Routes($config);
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