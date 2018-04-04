<?php
declare(strict_types=1);

use Composer\Satis\Webhook\Command\AddPackageCommand;
use Composer\Satis\Webhook\Command\UpdateRepositoryCommand;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouterInterface;

return [
    ContainerInterface::class => null,
    RouterInterface::class => [
        'root' => [
            'pattern' => '/command',
            'type' => 'group',
            'routes' => [
                'add' => [
                    'pattern' => '/add/{key}',
                    'type' => 'route',
                    'methods' => ['POST'],
                    'handler' => AddPackageCommand::class
                ],
                'update' => [
                    'pattern' => '/update/{key}',
                    'type' => 'route',
                    'methods' => ['POST'],
                    'handler' => UpdateRepositoryCommand::class
                ]
            ]
        ],
    ],
];
