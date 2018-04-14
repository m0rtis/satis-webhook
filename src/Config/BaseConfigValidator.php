<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Config;


use InvalidArgumentException;
use m0rtis\SimpleBox\Container;


/**
 * Class BaseConfigValidator
 * @package Composer\Satis\Webhook\Config
 */
abstract class BaseConfigValidator extends Container
{
    public const STRING = 'string';
    public const BOOL = 'bool';
    public const ARRAY = 'array';
    public const INT = 'int';
    public const FLOAT = 'float';

    /**
     * BaseConfigValidator constructor.
     * @param iterable $config
     */
    public function __construct(iterable $config)
    {
        $config = $this->validate($config);
        parent::__construct($config);
    }

    /**
     * @param iterable $config
     * @return iterable
     */
    abstract protected function validate(iterable $config): iterable;

    /**
     * @param iterable $item
     * @param array $requiredKeys
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    protected function check(iterable $item, array $requiredKeys): void
    {
        foreach ($requiredKeys as $requiredKey => $types) {
            if (!isset($item[$requiredKey])) {
                throw new InvalidArgumentException(sprintf('Missing required key %s', $requiredKey));
            }

            if (\is_object($item[$requiredKey])) {
                $givenType = \get_class($item[$requiredKey]);
            } else {
                $givenType = \gettype($item[$requiredKey]);
            }
            $types = (array)$types;
            if (!$this->typeCheck($givenType, $types)) {
                throw new InvalidArgumentException(sprintf(
                    'Expected type of $s key is $s. $s given',
                    $requiredKey,
                    implode(', ', $types),
                    $givenType
                ));
            }
        }
    }

    /**
     * @param string $givenType
     * @param array $types
     * @return bool
     * @throws \ReflectionException
     */
    private function typeCheck(string $givenType, array $types): bool
    {
        if (\in_array($givenType, $types, true)) {
            $result = true;
        } else {
            $reflect = new \ReflectionClass($givenType);
            $interfaces = $reflect->getInterfaceNames();
            $result = \count(array_intersect($interfaces, $types)) > 0;
        }
        return $result;
    }
}