<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 01/10/2014
 * Time: 10:58
 */

namespace Snc\RedisBundle\Tests\Metadata;
use Snc\RedisBundle\Annotation\CacheableResult;



class MetadataTestClass {

    /**
     * @CacheableResult(ttl=1800)
     */
    public $name;

    /**
     * @CacheableResult(ttl=1800)
     */
    public function coucou()
    {

    }
}
