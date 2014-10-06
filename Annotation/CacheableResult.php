<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 30/09/2014
 * Time: 16:12
 */

namespace Kairos\CacheBundle\Annotation;

/**
 * @Annotation
 * @Target({"METHOD","PROPERTY"})
 */
class CacheableResult {

    /**
     * @var int
     */
    public $ttl;

    /**
     * @var null|string
     */
    public $cacheProvider;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->ttl = isset($data['ttl']) ? (int) $data['ttl']:0;
        $this->cacheProvider = isset($data['cache_provider']) ? (string) $data['cache_provider']:null;
    }
} 