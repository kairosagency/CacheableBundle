<?php

namespace Kairos\CacheBundle\Tests\Client\Phpredis;

use Kairos\CacheBundle\Client\Phpredis\Client;

/**
 * ClientTest
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Kairos\CacheBundle\Client\Phpredis\Client::getCommandString
     */
    public function testGetCommandString()
    {
        $method = new \ReflectionMethod(
            '\Kairos\CacheBundle\Client\Phpredis\Client', 'getCommandString'
        );

        $method->setAccessible(true);

        $name = 'foo';
        $arguments = array(array('chuck', 'norris'));

        $this->assertEquals(
            'FOO chuck norris', $method->invoke(new \Kairos\CacheBundle\Client\Phpredis\Client(array('alias' => 'bar')), $name, $arguments)
        );

        $arguments = array('chuck:norris');

        $this->assertEquals(
            'FOO chuck:norris', $method->invoke(new \Kairos\CacheBundle\Client\Phpredis\Client(array('alias' => 'bar')), $name, $arguments)
        );

        $arguments = array('chuck:norris fab:pot');

        $this->assertEquals(
            'FOO chuck:norris fab:pot', $method->invoke(new \Kairos\CacheBundle\Client\Phpredis\Client(array('alias' => 'bar')), $name, $arguments)
        );

        $arguments = array('foo' => 'bar', 'baz' => null);

        $this->assertEquals(
            'FOO foo bar baz <null>', $method->invoke(new \Kairos\CacheBundle\Client\Phpredis\Client(array('alias' => 'bar')), $name, $arguments)
        );
    }
}
