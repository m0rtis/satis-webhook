<?php
declare(strict_types=1);

namespace Composer\Satis\Webhook;


use Composer\Satis\Webhook\Config\HandlerValidator;
use Composer\Satis\Webhook\Config\RoutesValidator;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;
use Slim\Interfaces\RouteInterface;
use Slim\Interfaces\RouterInterface;

final class Handler extends App implements RequestHandlerInterface
{
    /**
     * Handler constructor.
     * @param iterable $config
     * @throws \InvalidArgumentException
     */
    public function __construct(iterable $config)
    {
        $config = $this->setDefaults($config, __DIR__.'/Config/*.config.php');
        $config = new HandlerValidator($config);
        $container = $this->initContainerFromConfig($config[ContainerInterface::class]);
        $container['config'] = $config;
        parent::__construct(\iterator_to_array($config), $container);
        $this->initRoutesFromConfig($config[RouterInterface::class]);
    }

    /**
     * @param iterable|ContainerInterface $containerConfig
     * @return ContainerInterface
     */
    private function initContainerFromConfig($containerConfig): ContainerInterface
    {
        $container = $containerConfig;
        if (\is_array($containerConfig)
            && isset($containerConfig['factory_class'])
            && class_exists($containerConfig['factory_class'])
        ) {
            $factory = new $containerConfig['factory_class']();
            $container = $factory($containerConfig);
        }

        return $container;
    }

    /**
     * @param iterable $config
     * @param string $globPattern
     * @return iterable
     */
    private function setDefaults(iterable $config, string $globPattern): iterable
    {
        $iterator = new \GlobIterator($globPattern, GLOB_BRACE);
        $defaults = [];
        /** @var \GlobIterator $file */
        foreach ($iterator as $file) {
            $defaults[] = require $file->getPathname();
        }
        $defaults = array_merge(...$defaults);

        return array_replace_recursive($defaults, $config);
    }

    /**
     * @param iterable $config
     */
    private function initRoutesFromConfig(iterable $config): void
    {
        $config = new RoutesValidator($config);
        foreach ($config as $name => $route) {
            if ('group' === $route['type']) {
                $routable = $this->group($route['pattern'], function() use ($route, $name) {
                    foreach ($route['routes'] as $groupName => $groupRoute) {
                        $this->map($groupRoute['methods'], $groupRoute['pattern'], $groupRoute['handler'])->setName($groupName);
                    }
                });
            } elseif ('route' === $route['type']) {
                $routable = $this->map($route['methods'], $route['pattern'], $route['handler'])->setName($name);
            }
            if (isset($route['middleware'])) {
                $middleware = (array) $route['middleware'];
                array_map(function ($item) use ($routable) {
                    $routable->add($item);
                }, $middleware);
            }
        }
    }

    /**
     * @param array $methods
     * @param string $pattern
     * @param callable|string $callable
     * @return RouteInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function map(array $methods, $pattern, $callable): RouteInterface
    {
        /** @var Container\Container $config */
        $config = $this->getContainer()->get('config');
        if ($config->has('uri_key')) {
            $key = $config->get('uri_key');
            $pattern = rtrim($pattern, '/').'/'.$key;
        }
        return parent::map($methods, $pattern, $callable);
    }

    /**
     * Handle the request and return a response.
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->run($request);
    }
}