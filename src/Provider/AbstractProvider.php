<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Provider;


use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractProvider implements ProviderInterface
{
    /**
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     */
    public function provide(ServerRequestInterface $request): ServerRequestInterface
    {
        $packageName = $this->getPackageName($request);
        $secret = $this->getSecretToken($request);
        $request = $request
            ->withAttribute('package_name', $packageName)
            ->withAttribute('secret', $secret);
        return $request;
    }

    protected function getPayload(ServerRequestInterface $request): array
    {
        $body = $request->getParsedBody();
        return (array)$body;
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    abstract protected function getPackageName(ServerRequestInterface $request): string;

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    abstract protected function getSecretToken(ServerRequestInterface $request): string;
}