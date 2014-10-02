<?php


namespace Kairos\CacheBundle\CacheProvider;


Interface SimpleCacheProviderInterface
{
    /**
     * @param $id
     * @return mixed
     */
    public function doFetch($id);

    /**
     * @param $id
     * @return mixed
     */
    public function doContains($id);

    /**
     * @param $id
     * @param $data
     * @param int $lifeTime
     * @return mixed
     */
    public function doSave($id, $data, $lifeTime = 0);

    /**
     * @param $id
     * @return mixed
     */
    public function doDelete($id);

    /**
     * @return mixed
     */
    public function doFlush();

}
