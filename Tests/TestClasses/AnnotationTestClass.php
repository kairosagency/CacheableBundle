<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 01/10/2014
 * Time: 10:58
 */

namespace Kairos\CacheBundle\Tests\TestClasses;
use Kairos\CacheBundle\Annotation\CacheableResult;



class AnnotationTestClass {

    public $name;

    /**
     * @CacheableResult(ttl=1800)
     */
    public function coucou()
    {
        return "coucou";
    }

    /**
     * @CacheableResult(ttl=1801, cache_provider="@kairos_cache.test_cache")
     */
    public function coucou2()
    {
        return "coucou2";
    }
}
