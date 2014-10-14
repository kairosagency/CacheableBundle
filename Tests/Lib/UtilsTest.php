<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 01/10/2014
 * Time: 10:58
 */

namespace Kairos\CacheableBundle\Tests\Lib;



use Kairos\CacheableBundle\Lib\Utils;

class UtilsTest extends \PHPUnit_Framework_TestCase
{

    public function testGoodServiceId()
    {
        $this->assertEquals('test', Utils::normalizeServiceId('@test'));
    }

    /**
     * @expectedException \Exception
     */
    public function testBadServiceId()
    {
        $this->assertEquals('test', Utils::normalizeServiceId('test'));
    }

    /**
     * @expectedException \Exception
     */
    public function testDoubleAtServiceId()
    {
        $this->assertEquals('test', Utils::normalizeServiceId('@@test'));
    }
}
