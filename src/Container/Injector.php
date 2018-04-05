<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Container;


use Psr\Container\ContainerInterface;

final class Injector implements DependencyInjectorInterface
{
    private $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $id
     * @return bool
     * @throws \ReflectionException
     */
    public function canInstantiate(string $id): bool
    {
        $answer = false;

        $container = $this->container;
        $self = $this;
        $constructor = (new \ReflectionClass($id))->getConstructor();
        $deps = array_filter($this->getDependencies($constructor), function ($dep) use ($container, $self) {
            if ($container->has($dep)) {
                return false;
            }
            return !$self->canInstantiate($dep);
        });
        if (empty($deps)) {
            $answer = true;
        }

        return $answer;
    }

    /**
     * @param string $id
     * @return object
     * @throws \ReflectionException
     */
    public function instantiate(string $id): object
    {
        $reflect = new \ReflectionClass($id);
        $deps = $this->getDependencies($reflect->getConstructor());
        $arguments = [];
        foreach ($deps as $name => $type) {
            if ($this->container->has($type)) {
                $arguments[$name] = $this->container->get($type);
            } elseif ($this->container->has($name)) {
                $arguments[$name] = $this->container->get($name);
            } else {
                $arguments[$name] = $this->instantiate($type);
            }
        }
        return $reflect->newInstanceArgs($arguments);
    }

    /**
     * @param \ReflectionMethod $constructor
     * @return array
     */
    private function getDependencies(?\ReflectionMethod $constructor): array
    {
        $deps = [];
        if ($constructor) {
            $deps = $constructor->getParameters();
            $deps = $this->getNames(array_filter($deps, function ($dep) {
                /** @var \ReflectionParameter $dep */
                return !$dep->isOptional();
            }));
        }
        return $deps;
    }

    /**
     * @param \ReflectionParameter[] $deps
     * @return string[]
     */
    private function getNames(array $deps): array
    {
        $names = [];
        foreach ($deps as $dep) {
            if (!$dep->hasType()) {
                continue;
            }
            $names[$dep->getName()] = $dep->getType()->getName();
        }
        return $names;
    }
}