<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Provider;


use Psr\Http\Message\ServerRequestInterface;

interface ProviderInterface
{
    /**
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     */
    public function provide(ServerRequestInterface $request): ServerRequestInterface;
}