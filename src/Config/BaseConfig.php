<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Config;
use InvalidArgumentException;


/**
 * Class BaseConfig
 * @package Composer\Satis\Webhook\Config
 */
abstract class BaseConfig implements \ArrayAccess, \Iterator, \Countable
{
    const STRING = 'string';
    const BOOL = 'bool';
    const ARRAY = 'array';
    const INT = 'int';
    const FLOAT = 'float';
    /**
     * @var iterable
     */
    protected $config;

    /**
     * BaseConfig constructor.
     * @param iterable $config
     */
    public function __construct(iterable $config)
    {
        $this->config = $this->validate($config);
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
     */
    protected function check(iterable $item, array $requiredKeys): void
    {
        foreach ($requiredKeys as $requiredKey => $type) {
            if (!array_key_exists($requiredKey, $item)) {
                throw new InvalidArgumentException(sprintf('Missing required key %s', $requiredKey));
            }
            $givenType = gettype($requiredKey);
            if ($givenType !== $type) {
                throw new InvalidArgumentException(sprintf(
                    'Expected type of $s key is $s. $s given',
                    $requiredKey,
                    $type,
                    $givenType
                ));
            }
        }
    }
    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return \current($this->config);
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        next($this->config);
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return key($this->config);
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return $this->key() !== null;
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        reset($this->config);
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->config);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->config[$offset];
        }
        return null;
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->config[$offset] = $value;
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->config[$offset]);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return \count($this->config);
    }
}