<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 01/10/2014
 * Time: 14:47
 */

namespace Kairos\CacheableBundle\Service;

use Doctrine\Common\Cache\Cache;
use Metadata\ClassMetadata;
use Metadata\MetadataFactory;

class CacheableProxyService {

    /**
     * @var Object
     */
    protected $service;

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $defaultCacheProvider;

    /**
     * @var ClassMetadata
     */
    protected $classMetadata;

    /**
     * @var int
     */
    protected $defaultTTl;

    /**
     * @param MetadataFactory $metadataFactory
     * @param Cache $cacheProvider
     */
    public function __construct(ClassMetadata $classMetadata, Cache $defaultCacheProvider, $service, $defaultTTl)
    {
        $this->classMetadata = $classMetadata;
        $this->defaultCacheProvider = $defaultCacheProvider;
        $this->defaultTTl = $defaultTTl;
        $this->service = $service;
    }

    /**
     * @return Cache
     */
    public function getCache() {
        return $this->defaultCacheProvider;
    }


    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $callable = array($this->service, $name);

        if(isset($this->classMetadata->methodMetadata[$name]) && $methodMetadata = $this->classMetadata->methodMetadata[$name]) {


            $key = $name.md5($name.serialize($arguments));
            if($this->defaultCacheProvider->contains($key) && $res = $this->defaultCacheProvider->fetch($key)) {
                return $res;
            }
            else {
                $res = call_user_func(array($this->service, $name), $arguments);

                if(!is_null($methodMetadata->ttl))
                    $ttl = $methodMetadata->ttl;
                elseif(!is_null($this->defaultTTl))
                    $ttl = $this->defaultTTl;
                else
                    throw new \Exception("At least one tll (default ttl or method ttl) should be set");

                $this->defaultCacheProvider->save($key, $res, $ttl);
                return $res;
            }
        }
        // use methode exists since it does not care about __call() unlike is_callable
        // see : http://fr2.php.net/manual/fr/function.method-exists.php#101507
        else if(method_exists($this->service, $name)) {
            return call_user_func($callable, $arguments);
        }

        trigger_error('Call to undefined method '.$this->classMetadata->name.'::'.$name.'()', E_USER_ERROR);
    }

}
