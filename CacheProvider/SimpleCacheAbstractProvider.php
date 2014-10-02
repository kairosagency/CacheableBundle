<?php


namespace Kairos\CacheBundle\CacheProvider;


abstract class SimpleCacheAbstractProvider
{


    /**
     * @param $id
     * @return mixed
     */
    abstract public function doFetch($id);

    /**
     * @param $id
     * @return mixed
     */
    abstract public function doContains($id);

    /**
     * @param $id
     * @param $data
     * @param int $lifeTime
     * @return mixed
     */
    abstract public function doSave($id, $data, $lifeTime = 0);

    /**
     * @param $id
     * @return mixed
     */
    abstract public function doDelete($id);

    /**
     * @return mixed
     */
    abstract public function doFlush();

    /**
     * @param mixed $key
     * @return string
     */
    public function generateSignature($key)
    {
        return $this->prefix.md5(json_encode($key, true));
    }
}
