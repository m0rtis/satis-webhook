<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Provider;


use Psr\Http\Message\ServerRequestInterface;

final class Gitlab extends AbstractProvider
{
    /**
     * @param ServerRequestInterface $request
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function getPackageName(ServerRequestInterface $request): string
    {
        $payload = $this->getPayload($request);
        if (!isset($payload['project']['path_with_namespaces'])) {
            throw new \InvalidArgumentException("Gitlab must provide 'project' array with 'path_with_namespaces' key");
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
        if (!$request->hasHeader('X-Gitlab-Token')) {
            throw new \InvalidArgumentException("Gitlab must provide seret in 'X-Gitlab-Token' header");
        }
        return $request->getHeaderLine('X-Gitlab-Token');
    }
}