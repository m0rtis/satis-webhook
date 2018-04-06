<?php
declare(strict_types=1);

use Composer\Satis\Webhook\AuthMiddleware;
use Composer\Satis\Webhook\Command\AddPackageCommand;
use Composer\Satis\Webhook\Command\UpdateRepositoryCommand;
use Composer\Satis\Webhook\Container\ContainerFactory;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouterInterface;

return [
    ContainerInterface::class => [
        'factory_class' => ContainerFactory::class,
        'definitions' => [

        ]
    ],
    RouterInterface::class => [
        'root' => [
            'pattern' => '/command',
            'type' => 'group',
            'routes' => [
                'add' => [
                    'pattern' => '/{provider}/add/',
                    'type' => 'route',
                    'methods' => ['POST'],
                    'handler' => AddPackageCommand::class,
                ],
                'update' => [
                    'pattern' => '/{provider}/update/',
                    'type' => 'route',
                    'methods' => ['POST'],
                    'handler' => UpdateRepositoryCommand::class
                ]
            ],
            'middleware' => AuthMiddleware::class
        ]
    ],
];
