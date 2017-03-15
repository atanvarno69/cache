<?php
/**
 * @package   Atanvarno\Cache
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atanvarno\Cache\Test;

/** PSR-16 use block. */
use Psr\SimpleCache\CacheException as CacheExceptionInterface;

/** PHPUnit use block. */
use PHPUnit\Framework\TestCase;

/** Package use block. */
use Atanvarno\Cache\Exception\CacheException;

class CacheExceptionTest extends TestCase
{
    public function testImplementsInterface()
    {
        $exception = new CacheException();
        $this->assertInstanceOf(CacheExceptionInterface::class, $exception);
    }
}
