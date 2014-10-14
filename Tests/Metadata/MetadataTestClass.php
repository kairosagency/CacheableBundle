<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 01/10/2014
 * Time: 10:58
 */

namespace Kairos\CacheableBundle\Tests\Metadata;
use Kairos\CacheableBundle\Annotation\CacheProviderAnnotation;



class MetadataTestClass {

    /**
     * @CacheableResult(ttl=1800)
     */
    public $name;

    /**
     * @CacheableResult(ttl=1800, cache_provider=@kairos_cache.default_cache)
     */
    public function coucou()
    {

    }
}
