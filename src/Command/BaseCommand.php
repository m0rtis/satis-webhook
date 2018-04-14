<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Command;


use Composer\IO\NullIO;
use Composer\Satis\Console\Application;
use Composer\Satis\Webhook\Config\CommandValidator;
use Composer\Satis\Webhook\Provider\ProviderInterface;
use Composer\Satis\Webhook\UnauthorizedRequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class BaseCommand
 * @package Composer\Satis\Webhook\Command
 */
abstract class BaseCommand
{
    /**
     * @var CommandValidator
     */
    protected $config;
    /**
     * @var ArrayInput
     */
    protected $input;
    /**
     * @var BufferedOutput
     */
    protected $output;

    /**
     * BaseCommand constructor.
     * @param iterable $config
     */
    public function __construct(iterable $config)
    {
        $this->config = new CommandValidator($config);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $answer = '';
        try {
            $answer = $this->execute($request, $args['provider']);
        } catch (UnauthorizedRequestException $e) {
            $response = $response->withStatus(401);
            //TODO: To implement logging
        } catch (\Exception $e) {
            $response = $response->withStatus(500);
            $answer = $e->getMessage();
        }
        $response->getBody()->write($answer);
        return $response;
    }

    /**
     * @param ServerRequestInterface $request
     * @param string $provider
     * @return string
     * @throws \InvalidArgumentException
     * @throws UnauthorizedRequestException
     * @throws \ReflectionException
     */
    protected function execute(ServerRequestInterface $request, string $provider): string
    {
        $request = $this->prepareRequest($request, $provider);
        return $this->runSatisCommands($request);
    }

    /**
     * @param string $provider
     * @return ProviderInterface
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    protected function getProvider(string $provider): ProviderInterface
    {
        $name = ucfirst(strtolower($provider));
        $namespace = (new \ReflectionClass(ProviderInterface::class))->getNamespaceName();
        $providerClass = $namespace.'\\'.$name;
        if (\class_exists($providerClass)) {
            return new $providerClass();
        }
        throw new \InvalidArgumentException("Provider class $providerClass does not exist");
    }

    /**
     * @param string $commandName
     * @return Command
     * @throws CommandNotFoundException
     * @throws \ReflectionException
     */
    protected function getCommand(string $commandName): Command
    {
        $app = new Application();
        $command = $app->find($commandName);
        $command = $this->configureCommand($command, $app);
        return $command;
    }

    /**
     * @param Command $command
     * @param Application $app
     * @return Command
     * @throws \ReflectionException
     */
    private function configureCommand(Command $command, Application $app): Command
    {
        $io = new NullIO();
        $refApp = new \ReflectionClass($app);
        $ioProp = $refApp->getProperty('io');
        $ioProp->setAccessible(true);
        $ioProp->setValue($app, $io);
        $command->setApplication($app);
        return $command;
    }

    /**
     * @param ServerRequestInterface $request
     * @param string $provider
     * @return ServerRequestInterface
     * @throws UnauthorizedRequestException
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    private function prepareRequest(ServerRequestInterface $request, string $provider): ServerRequestInterface
    {
            $provider = $this->getProvider($provider);
            $request = $provider->provide($request);
            if (isset($this->config['secret'])) {
                $this->checkSecret($request);
            }
            return $request;
    }

    /**
     * @param ServerRequestInterface $request
     * @throws UnauthorizedRequestException
     */
    private function checkSecret(ServerRequestInterface $request): void
    {
        $givenSecret = $request->getAttribute('secret');
        if ($givenSecret !== $this->config['secret']) {
            throw new UnauthorizedRequestException($givenSecret);
        }
    }

    abstract protected function runSatisCommands(ServerRequestInterface $request): string;
}