<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Command;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpForbiddenException;

final class UpdateRepositoryCommand extends BaseCommand
{

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     * @throws HttpForbiddenException
     * @throws \ReflectionException
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        if (!$this->checkKey($args['key'])) {
            throw new HttpForbiddenException($request, 'Invalid uri key. Provided: ' .$args['key']);
        }
        $provider = $this->getProvider($args['provider']);
        $request = $provider->provide($request);
        $app = $this->setUp();
        $this->input->setArgument('packages', $request->getAttribute('package_name'));
        $this->input->setArgument('command', 'build');
        try {
            $app->run($this->input, $this->output);
        } catch (\Exception $e) {
            $body = $response->getBody();
            $body->write($e->getMessage());
            $response = $response->withBody($body);
        }
        return $response;
    }
}