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
     * @param string $className
     * @return bool
     * @throws \ReflectionException
     */
    public function canInstantiate(string $className): bool
    {
        $answer = false;
        $container = $this->container;
        $self = $this;
        $constructor = (new \ReflectionClass($className))->getConstructor();
        $deps = array_filter($this->getDependencies($constructor), function ($type, $name) use ($container, $self) {
            if ($container->has($type) || $container->has($name)) {
                return false;
            }
            if (\class_exists($type)) {
                return !$self->canInstantiate($type);
            }
            return true;
        }, ARRAY_FILTER_USE_BOTH);
        if (empty($deps)) {
            $answer = true;
        }

        return $answer;
    }

    /**
     * @param string $className
     * @return object
     * @throws \ReflectionException
     */
    public function instantiate(string $className): object
    {
        $reflect = new \ReflectionClass($className);
        $deps = $this->getDependencies($reflect->getConstructor());
        $arguments = [];
        foreach ($deps as $name => $type) {
            if ($this->container->has($type)) {
                $arguments[$name] = $this->container->get($type);
            } elseif ($this->container->has($name)) {
                $arguments[$name] = $this->container->get($name);
            } elseif (\class_exists($type)) {
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
            $deps = $this->getNames(array_filter($constructor->getParameters(), function ($dep) {
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