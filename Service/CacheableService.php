<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 01/10/2014
 * Time: 14:47
 */

namespace Snc\RedisBundle\Service;


class CacheableService {
    /**
     * @var int
     */
    public $redis;


    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->ttl = isset($data['ttl']) ? (int) $data['ttl']:0;
    }
} 