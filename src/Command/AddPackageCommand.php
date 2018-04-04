<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Command;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class AddPackageCommand
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

    }
}