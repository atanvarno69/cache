<?php
/**
 * @package   Atanvarno\Cache
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atanvarno\Cache\Exception;

/** SPL use block. */
use Exception;

/** PSR-16 use block. */
use Psr\SimpleCache\CacheException as CacheExceptionInterface;

/**
 * Atanvarno\Cache\Exception\CacheException
 *
 * @api
 */
class CacheException extends Exception implements CacheExceptionInterface
{

}
