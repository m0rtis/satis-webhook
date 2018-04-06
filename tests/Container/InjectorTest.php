<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Test\Container;

use Composer\Satis\Webhook\Command\UpdateRepositoryCommand;


final class InjectorTest extends ContainerTest
{
    public function testGetAutowiring(): void
    {
        $container = $this->getContainer([
            'config' => require __DIR__.'/../../config/example.config.php'
        ]);
        $result = $container->get(UpdateRepositoryCommand::class);

        $this->assertInstanceOf(UpdateRepositoryCommand::class, $result);
    }
}