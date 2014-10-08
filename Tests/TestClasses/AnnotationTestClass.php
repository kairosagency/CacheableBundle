<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 01/10/2014
 * Time: 10:58
 */

namespace Kairos\CacheBundle\Tests\TestClasses;
use Kairos\CacheBundle\Annotation as KairosCache;


/**
 * @KairosCache\CacheProvider("@doctrine.test_cache")
 */
class AnnotationTestClass {

    public $name;

    /**
     * @KairosCache\TTL(1800)
     */
    public function coucou()
    {
        return "coucou";
    }

    /**
     * @KairosCache\TTL(1801)
     */
    public function coucou2()
    {
        return "coucou2";
    }
}
