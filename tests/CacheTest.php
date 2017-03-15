<?php
/**
 * @package   Atanvarno\Cache
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atanvarno\Cache\Test;

/** SPL use block. */
use ArrayObject, DateInterval, TypeError;

/** PSR-16 use block. */
use Psr\SimpleCache\CacheInterface;

/** PHPUnit use block. */
use PHPUnit\Framework\TestCase;

/** Package use block. */
use Atanvarno\Cache\{
    Cache, Driver, Exception\InvalidArgumentException
};

class CacheTest extends TestCase
{
    /** @var Cache $cache */
    private $cache;

    /** @var \PHPUnit_Framework_MockObject_MockObject $driver */
    private $driver;

    public function setUp()
    {
        $this->driver = $this->createMock(Driver::class);
        $this->cache = new Cache($this->driver);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf(CacheInterface::class, $this->cache);
    }

    public function testGet()
    {
        $this->driver->expects($this->once())
            ->method('get')
            ->with('test', null);
        $this->cache->get('test');
    }

    public function testGetWithDefaultValue()
    {
        $this->driver->expects($this->once())
            ->method('get')
            ->with('test', 'default');
        $this->cache->get('test', 'default');
    }

    public function testGetRejectsNonStringParameter()
    {
        $this->expectException(TypeError::class);
        $this->cache->get(5);
    }

    public function testGetRejectsIllegalParameter()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->cache->get(':illegal');
    }

    public function testSet()
    {
        $this->driver->expects($this->once())
            ->method('set')
            ->with('test', 'value', 0);
        $this->cache->set('test', 'value');
    }

    public function testSetAcceptsIntTtl()
    {
        $this->driver->expects($this->once())
            ->method('set')
            ->with('test', 'value', 10);
        $this->cache->set('test', 'value', 10);
    }

    public function testSetAcceptsDateIntervalTtl()
    {
        $this->driver->expects($this->once())
            ->method('set')
            ->with('test', 'value', 10);
        $ttl = new DateInterval('PT10S');
        $this->cache->set('test', 'value', $ttl);
    }

    public function testGetRejectsNonStringKey()
    {
        $this->expectException(TypeError::class);
        $this->cache->set(5, 'value');
    }

    public function testGetRejectsIllegalTtl()
    {
        $this->expectException(TypeError::class);
        $this->cache->set('test', 'value', 'illegal');
    }

    public function testGetRejectsIllegalKey()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->cache->set(':illegal', 'value');
    }

    public function testDelete()
    {
        $this->driver->expects($this->once())
            ->method('delete')
            ->with('test');
        $this->cache->delete('test');
    }

    public function testDeleteRejectsNonStringKey()
    {
        $this->expectException(TypeError::class);
        $this->cache->delete(5);
    }

    public function testDeleteRejectsIllegalKey()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->cache->delete(':illegal');
    }

    public function testClear()
    {
        $this->driver->expects($this->once())
            ->method('clear');
        $this->cache->clear();
    }

    public function testHas()
    {
        $this->driver->expects($this->once())
            ->method('has')
            ->with('test');
        $this->cache->has('test');
    }

    public function testHasRejectsNonStringKey()
    {
        $this->expectException(TypeError::class);
        $this->cache->has(5);
    }

    public function testHasRejectsIllegalKey()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->cache->has(':illegal');
    }

    public function testGetMultiple()
    {
        $this->driver->expects($this->at(0))
            ->method('get')
            ->with('test');
        $this->driver->expects($this->at(1))
            ->method('get')
            ->with('id');
        $this->cache->getMultiple(['test', 'id']);
    }

    public function testGetMultipleWithIterator()
    {
        $this->driver->expects($this->at(0))
            ->method('get')
            ->with('test');
        $this->driver->expects($this->at(1))
            ->method('get')
            ->with('id');
        $iterator = new ArrayObject(['test', 'id']);
        $this->cache->getMultiple($iterator);
    }

    public function testGetMultipleRejectsNonIterator()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->cache->getMultiple(new \StdClass());
    }

    public function testSetMultiple()
    {
        $this->driver->expects($this->at(0))
            ->method('set')
            ->with('keyA', 'valueA', 0)
            ->willReturn(true);
        $this->driver->expects($this->at(1))
            ->method('set')
            ->with('keyB', 'valueB', 0)
            ->willReturn(true);
        $result = $this->cache->setMultiple(
            ['keyA' => 'valueA', 'keyB' => 'valueB']
        );
        $this->assertTrue($result);
    }

    public function testSetMultipleWithIterator()
    {
        $this->driver->expects($this->at(0))
            ->method('set')
            ->with('keyA', 'valueA', 0)
            ->willReturn(true);
        $this->driver->expects($this->at(1))
            ->method('set')
            ->with('keyB', 'valueB', 0)
            ->willReturn(true);
        $iterator = new ArrayObject(['keyA' => 'valueA', 'keyB' => 'valueB']);
        $result = $this->cache->setMultiple($iterator);
        $this->assertTrue($result);
    }

    public function testSetMultipleRejectsNonIterator()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->cache->setMultiple(new \StdClass());
    }

    public function testDeleteMultiple()
    {
        $this->driver->expects($this->at(0))
            ->method('delete')
            ->with('test')
            ->willReturn(true);
        $this->driver->expects($this->at(1))
            ->method('delete')
            ->with('id')
            ->willReturn(true);
        $result = $this->cache->deleteMultiple(['test', 'id']);
        $this->assertTrue($result);
    }

    public function testDeleteMultipleWithIterator()
    {
        $this->driver->expects($this->at(0))
            ->method('delete')
            ->with('test')
            ->willReturn(true);
        $this->driver->expects($this->at(1))
            ->method('delete')
            ->with('id')
            ->willReturn(true);
        $iterator = new ArrayObject(['test', 'id']);
        $result = $this->cache->deleteMultiple($iterator);
        $this->assertTrue($result);
    }

    public function testDeleteMultipleRejectsNonIterator()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->cache->deleteMultiple(new \StdClass());
    }
}
