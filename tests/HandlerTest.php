<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Test;


use Composer\Satis\Webhook\Command\UpdateRepositoryCommand;
use Composer\Satis\Webhook\Handler;
use Composer\Satis\Webhook\Test\Mock\UpdateRepositoryCommandMock;
use m0rtis\SimpleBox\ContainerFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Interfaces\RouterInterface;
use Slim\Routable;

final class HandlerTest extends TestCase
{
    public function testInitContainerFromConfigAndSetDefaults(): void
    {
        $config = TestHelper::getConfig();
        $container = (new ContainerFactory())();
        $config[ContainerInterface::class] = $container;
        $handler1 = new Handler($config);
        $handler2 = new Handler(TestHelper::getConfig());

        $this->assertArrayNotHasKey('factory_class', $handler1->getSetting(ContainerInterface::class));
        $this->assertSame($container, $handler1->getContainer());
        $this->assertInstanceOf(ContainerInterface::class, $handler2->getContainer());
    }

    public function testInitRoutesSingleRoute(): void
    {
        $config = [
            RouterInterface::class => [
                'test' => [
                    'type' => 'route',
                    'pattern' => '/test/',
                    'methods' => ['GET', 'POST'],
                    'handler' => UpdateRepositoryCommand::class
                ]
            ]
        ];
        $handler = new Handler($config);
        $route = $handler->getRouter()->getNamedRoute('test');

        $this->assertInstanceOf(Routable::class, $route);
        $this->assertEquals('/test/', $route->getPattern());
    }

    public function testInitRouteGroupRoute(): void
    {
        $handler = new Handler([]);
        $router = $handler->getRouter();
        $path = $router->pathFor('update', [
            'provider' => 'test',
        ]);

        $this->assertEquals('/command/test/update/', $path);
    }

    public function testAddUriKey(): void
    {
        $handler = new Handler(['uri_key' => '1234567']);
        $router = $handler->getRouter();
        $path = $router->pathFor('update', [
            'provider' => 'test',
        ]);

        $this->assertSame('/command/test/update/1234567', $path);
    }

    public function testHandle(): void
    {
        $globals = TestHelper::getGlobals();
        $request = Request::createFromGlobals($globals);
        $request = $request->withParsedBody(TestHelper::getGitlabAnswer());

        $config = TestHelper::getConfig();
        $config[RouterInterface::class]['root']['routes']['update']['handler'] = UpdateRepositoryCommandMock::class;

        $handler = new Handler($config);
        $response = $handler->handle($request);
        $body = (string)$response->getBody();
        $bodyArray = explode("\n", trim($body));

        $this->assertCount(2, $bodyArray);
        $this->assertContains('Add', $bodyArray[0]);
        $this->assertContains('Build', $bodyArray[1]);
    }
}