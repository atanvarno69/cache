<?php
/**
 * @package   Atanvarno\Cache
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atanvarno\Cache;

/** SPL use block. */
use DateInterval, DateTimeImmutable, Traversable, TypeError;

/** PSR-16 use block. */
use Psr\SimpleCache\CacheInterface;

/** Package use block. */
use Atanvarno\Cache\Exception\InvalidArgumentException;

/**
 * Atanvarno\Cache\Cache
 *
 * @api
 */
class Cache implements CacheInterface
{
    /** @var Driver $driver */
    private $driver;

    /**
     * Cache constructor.
     *
     * @param Driver $driver
     */
    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
    }

    /** @inheritdoc */
    public function get($key, $default = null)
    {
        if (!is_string($key)) {
            $msg = $this->getBcErrorMsg(1, __METHOD__, 'string', $key);
            throw new TypeError($msg);
        }
        if (!$this->isKeyLegal($key)) {
            $msg = sprintf('%s is not a legal key', $key);
            throw new InvalidArgumentException($msg);
        }
        return $this->driver->get($key, $default);
    }

    /** @inheritdoc */
    public function set($key, $value, $ttl = null): bool
    {
        if (!is_string($key)) {
            $msg = $this->getBcErrorMsg(1, __METHOD__, 'string', $key);
            throw new TypeError($msg);
        }
        if (!is_null($ttl) && !is_int($ttl) && !$ttl instanceof DateInterval) {
            $msg = $this->getBcErrorMsg(
                1, __METHOD__, 'int or DateInterval', $key
            );
            throw new TypeError($msg);
        }
        if (!$this->isKeyLegal($key)) {
            $msg = sprintf('%s is not a legal key', $key);
            throw new InvalidArgumentException($msg);
        }
        if (is_null($ttl)) {
            $ttl = 0;
        }
        if ($ttl instanceof DateInterval) {
            $ttl = $this->dateIntervalToSeconds($ttl);
        }
        return $this->driver->set($key, $value, $ttl);
    }

    /** @inheritdoc */
    public function delete($key): bool
    {
        if (!is_string($key)) {
            $msg = $this->getBcErrorMsg(1, __METHOD__, 'string', $key);
            throw new TypeError($msg);
        }
        if (!$this->isKeyLegal($key)) {
            $msg = sprintf('%s is not a legal key', $key);
            throw new InvalidArgumentException($msg);
        }
        return $this->driver->delete($key);
    }

    /** @inheritdoc */
    public function clear(): bool
    {
        return $this->driver->clear();
    }

    /** @inheritdoc */
    public function getMultiple($keys, $default = null): array
    {
        if (!$this->isIterable($keys)) {
            $msg = $this->getBcErrorMsg(1, __METHOD__, 'iterable', $keys);
            throw new InvalidArgumentException($msg);
        }
        $return = [];
        foreach ($keys as $key => $value) {
            $return[$key] = $this->get($value, $default);
        }
        return $return;
    }

    /** @inheritdoc */
    public function setMultiple($values, $ttl = null): bool
    {
        if (!$this->isIterable($values)) {
            $msg = $this->getBcErrorMsg(1, __METHOD__, 'iterable', $values);
            throw new InvalidArgumentException($msg);
        }
        $return = true;
        foreach ($values as $key => $value) {
            $result = $this->set($key, $value, $ttl);
            $return = (!$result) ? false : $return;
        }
        return $return;
    }

    /** @inheritdoc */
    public function deleteMultiple($keys): bool
    {
        if (!$this->isIterable($keys)) {
            $msg = $this->getBcErrorMsg(1, __METHOD__, 'iterable', $keys);
            throw new InvalidArgumentException($msg);
        }
        $return = true;
        foreach ($keys as $key) {
            $result = $this->delete($key);
            $return = (!$result) ? false : $return;
        }
        return $return;
    }

    /** @inheritdoc */
    public function has($key): bool
    {
        if (!is_string($key)) {
            throw new TypeError(
                $this->getBcErrorMsg(1, __METHOD__, 'string', $key)
            );
        }
        if (!$this->isKeyLegal($key)) {
            $msg = sprintf('%s is not a legal key', $key);
            throw new InvalidArgumentException($msg);
        }
        return $this->driver->has($key);
    }

    private function isIterable($value): bool
    {
        if (is_array($value)) {
            return true;
        }
        if ($value instanceof Traversable) {
            return true;
        }
        return false;
    }

    private function isKeyLegal(string $key): bool
    {
        $illegal = ['{', '}', '(', ')', '/', '\\', '@', ':'];
        foreach ($illegal as $needle) {
            if (strpos($key, $needle) !== false) {
                return false;
            }
        }
        return true;
    }

    private function dateIntervalToSeconds(DateInterval $dateInterval): int
    {
        $reference = new DateTimeImmutable;
        $endTime = $reference->add($dateInterval);
        return $endTime->getTimestamp() - $reference->getTimestamp();
    }

    private function getBcErrorMsg(
        int $arg,
        string $method,
        string $expected,
        $actual
    ): string {
        return sprintf(
            'Argument %u passed to %s must be of the type %s, %s given',
            $arg,
            $method,
            $expected,
            gettype($actual)
        );
    }
}