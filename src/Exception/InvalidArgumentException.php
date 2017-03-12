<?php
/**
 * @package   Atanvarno\Cache
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atanvarno\Cache\Exception;

/** PSR-16 use block. */
use Psr\SimpleCache\InvalidArgumentException as InvalidArgumentExceptionInterface;

/**
 * Atanvarno\Cache\Exception\InvalidArgumentException
 *
 * @api
 */
class InvalidArgumentException extends CacheException implements
    InvalidArgumentExceptionInterface
{

}
