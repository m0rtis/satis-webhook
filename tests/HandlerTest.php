<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Test;


use Composer\Satis\Webhook\Command\AddPackageCommand;
use Composer\Satis\Webhook\Container\ContainerFactory;
use Composer\Satis\Webhook\Handler;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouterInterface;
use Slim\Routable;

final class HandlerTest extends TestCase
{
    private $config;

    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        $this->config = require __DIR__.'/../config/example.config.php';
    }

    public function testInitContainerFromConfigAndSetDefaults(): void
    {
        $config = $this->config;
        $container = (new ContainerFactory())();
        $config[ContainerInterface::class] = $container;
        $handler1 = new Handler($config);
        $handler2 = new Handler($this->config);

        $this->assertArrayNotHasKey('factory_class', $handler1->getSetting(ContainerInterface::class));
        $this->assertSame($container, $handler1->getContainer());
        $this->assertInstanceOf(ContainerInterface::class, $handler2->getContainer());
    }

    public function testInitRoutes()
    {
        $config = [
            RouterInterface::class => [
                'test' => [
                    'type' => 'route',
                    'pattern' => '/test/',
                    'methods' => ['GET', 'POST'],
                    'handler' => AddPackageCommand::class
                ]
            ]
        ];
        $handler = new Handler($config);
        $route = $handler->getRouter()->getNamedRoute('test');
        $this->assertInstanceOf(Routable::class, $route);
    }
}