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
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->ttl = isset($data['ttl']) ? (int) $data['ttl']:0;
    }
} 