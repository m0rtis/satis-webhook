<?php
declare(strict_types=1);

use Composer\Satis\Webhook\Command\AddPackageCommand;
use Composer\Satis\Webhook\Command\UpdateRepositoryCommand;
use m0rtis\SimpleBox\ContainerFactory;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouterInterface;

return [
    ContainerInterface::class => [
        'factory_class' => ContainerFactory::class
    ],
    RouterInterface::class => [
        'root' => [
            'pattern' => '/command',
            'type' => 'group',
            'routes' => [
                'update' => [
                    'pattern' => '/{provider}/update/',
                    'type' => 'route',
                    'methods' => ['POST'],
                    'handler' => UpdateRepositoryCommand::class
                ]
            ]
        ]
    ]
];
