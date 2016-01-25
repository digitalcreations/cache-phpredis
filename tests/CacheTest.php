<?php

class CacheTest extends PHPUnit_Framework_TestCase {
    private function getCache() {
        return new \DC\Cache\Implementations\Redis\Cache("127.0.0.1", 6379);
    }

    function testInsertAndRetrieve() {
        $cache = $this->getCache();
        $cache->set('foo', 'bar');
        $this->assertEquals('bar', $cache->get('foo'));
    }

    function testDeleteAllAndRetrieve() {
        $cache = $this->getCache();
        $cache->set('foo', 'bar', 10);
        $this->assertEquals('bar', $cache->get('foo'));

        $cache->deleteAll();
        // TODO: return something besides false when not found
        $this->assertFalse($cache->get('foo'));
    }

    function testSetExpiredAndRetrieve() {
        $cache = $this->getCache();
        $cache->set('foo', 'bar', new \DateTime("@0")); // expired in 1970

        // TODO: return something besides false when not found
        $this->assertFalse($cache->get('foo'));
    }

    function testGetWithFallbackWhenKeyExists() {
        $cache = $this->getCache();
        $cache->set('foo', 'bar');

        $this->assertEquals('bar', $cache->getWithFallback('foo', function() {
            $this->fail('fallback should not be called');
        }));
    }

    function testGetWithFallbackWhenKeyDoesNotExist() {
        $cache = $this->getCache();
        $cache->delete('foo');

        $this->assertEquals('bar', $cache->getWithFallback('foo', function() {
            return 'bar';
        }));

        $this->assertEquals('bar', $cache->get('foo'));
    }

    function testGetWithFallbackAndValidityCallback() {
        $cache = $this->getCache();
        $cache->delete('foo');

        $validityCalled = false;
        $this->assertEquals('bar',
            $cache->getWithFallback('foo',
                function() {
                    return 'bar';
                },
                function($value) use (&$validityCalled) {
                    $validityCalled = true;
                    $this->assertEquals('bar', $value);
                    return new \DateInterval("PT1M");
                }));

        $this->assertTrue($validityCalled);
        $this->assertEquals('bar', $cache->get('foo'));
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    function testGetWithFallbackValidityCallbackReturnsStrangeValue() {
        $cache = $this->getCache();
        $cache->delete('foo');

        $this->assertEquals('bar',
            $cache->getWithFallback('foo',
                function() { return 'bar'; },
                function() { return "asd"; }
            ));
    }
}
 