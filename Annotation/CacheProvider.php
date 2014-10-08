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
 * @Target({"CLASS"})
 */
class CacheProvider {

    /**
     * @var null|string
     */
    public $cacheProvider;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->cacheProvider = isset($data['value']) ? (string) $data['value']:null;
    }
} 