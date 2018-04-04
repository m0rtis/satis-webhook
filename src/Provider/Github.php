<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Provider;


use Psr\Http\Message\ServerRequestInterface;

final class Github extends AbstractProvider
{
    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    protected function getPackageName(ServerRequestInterface $request): string
    {
        // TODO: Implement getPackageName() method.
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    protected function getSecretToken(ServerRequestInterface $request): string
    {
        // TODO: Implement getSecretToken() method.
    }
}