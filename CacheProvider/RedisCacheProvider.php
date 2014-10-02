<?php

/*
 * This file is part of the SncRedisBundle package.
 *
 * (c) Henrik Westphal <henrik.westphal@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kairos\CacheBundle\CacheProvider;

/**
 * Redis based session storage with session locking support
 *
 * @author Justin Rainbow <justin.rainbow@gmail.com>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Henrik Westphal <henrik.westphal@gmail.com>
 * @author Maurits van der Schee <maurits@vdschee.nl>
 */
class RedisCacheProvider extends SimpleCacheAbstractProvider
{
    /**
     * @var \Predis\Client|\Redis
     */
    protected $redis;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var integer Default PHP max execution time in seconds
     */
    const DEFAULT_MAX_EXECUTION_TIME = 30;


    /**
     * Redis session storage constructor
     *
     * @param \Predis\Client|\Redis $redis   Redis database connection
     * @param array                 $options Session options
     * @param string                $prefix  Prefix to use when writing session data
     */
    public function __construct($redis, array $options = array(), $prefix = 'cache')
    {
        $this->redis = $redis;
        $this->prefix = $prefix;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function doFetch($id)
    {
        return $this->redis->get((string) $id) ?: '';
    }

    /**
     * @param $id
     * @return boolean
     */
    public function doContains($id)
    {
        return $this->redis->exists((string) $id);
    }

    /**
     * @param $id
     * @param $data
     * @param int $lifeTime
     * @return boolean
     */
    public function doSave($id, $data, $lifeTime = 0)
    {
        $result = false;

        if($lifeTime > 0) {
            $result = $this->redis->setex((string) $id, $lifeTime, $data);
        }
        else {
            $result = $this->redis->set((string) $id, $data);
        }
        return $result;
    }

    /**
     * @param $id
     * @return boolean
     */
    public function doDelete($id)
    {
        return $this->redis->del((string) $id) > 0;
    }

    /**
     * @return boolean
     */
    public function doFlush()
    {
        return $this->redis->flushDB();
    }

}
