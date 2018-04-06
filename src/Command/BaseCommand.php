<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Command;


use Composer\Satis\Console\Application;
use Composer\Satis\Webhook\Config\CommandValidator;
use Composer\Satis\Webhook\Provider\ProviderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

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
     * @var NullOutput
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
     * @return Application
     */
    protected function setUp(): Application
    {
        $this->input = new ArrayInput([
            'file' => $this->config->get('satis_config'),
            'output-dir' => $this->config->get('output_dir')
        ]);
        $this->output = new NullOutput();
        return new Application();
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
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    abstract public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args);
}