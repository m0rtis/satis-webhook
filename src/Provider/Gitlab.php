<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Provider;


use Psr\Http\Message\ServerRequestInterface;

final class Gitlab extends AbstractProvider
{
    /**
     * @param ServerRequestInterface $request
     * @return string
     * @throws \RuntimeException
     */
    protected function getPackageName(ServerRequestInterface $request): string
    {
        $payload = $this->getPayload($request);
        if (!isset($payload['project']['path_with_namespace'])) {
            throw new \RuntimeException("Gitlab must provide 'project' array with 'path_with_namespace' key");
        }
        return $payload['project']['path_with_namespaces'];
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function getSecretToken(ServerRequestInterface $request): string
    {
        $token = '';
        if ($request->hasHeader('X-Gitlab-Token')) {
            $token = $request->getHeaderLine('X-Gitlab-Token');
        }
        return $token;
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     * @throws \RuntimeException
     */
    protected function getPackageUrl(ServerRequestInterface $request): string
    {
        $payload = $this->getPayload($request);
        if (!isset($payload['project']['url'])) {
            throw new \RuntimeException("Gitlab must provide 'project' array with 'path_with_namespaces' key");
        }
        return $payload['project']['url'];
    }
}