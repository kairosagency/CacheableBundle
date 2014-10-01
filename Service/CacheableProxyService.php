<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 01/10/2014
 * Time: 14:47
 */

namespace Snc\RedisBundle\Service;

use Metadata\MergeableClassMetadata;

class CacheableProxyService {

    protected $objectToCache;
    protected $cacheProvider;
    protected $metadataParser;
    /**
     * @var MergeableClassMetadata
     */
    protected $classMetadata;

    /**
     * @param array $data
     */
    public function __construct($metadataParser, $cacheProvider)
    {
        $this->metadataParser = $metadataParser;
        $this->cacheProvider = $cacheProvider;
    }

    public function setObjectToCache ($objectToCache) {
        if(!is_object($objectToCache))
            throw new \Exception("Object to cache is not an Object");

        if(is_null($this->objectToCache)) {
            $this->objectToCache = $objectToCache;
            $this->classMetadata = $this->metadataParser->loadMetadataForClass(get_class($objectToCache));
        } else {
            throw new \Exception("Object has already been set");
        }
    }


    public function __call($name, $arguments)
    {
        $callable = array($this->objectToCache, $name);

        if(isset($this->classMetadata->methodMetadata[$name]) && $methodMetadata = $this->classMetadata->methodMetadata[$name]) {
            $key = md5($name.serialize($arguments));
            if($res = $this->cacheProvider->get($key)) {
                return $res;
            }
            else {
                $res = call_user_func(array($this->objectToCache, $name), $arguments);
                $this->cacheProvider->set($key, $res, $methodMetadata->ttl);
                return $res;
            }
        }
        // use methode exists since it does not care about __call() unlike is_callable
        // see : http://fr2.php.net/manual/fr/function.method-exists.php#101507
        else if(method_exists($this->objectToCache, $name)) {
            return call_user_func($callable, $arguments);
        }

        trigger_error('Call to undefined method '.$this->classMetadata->name.'::'.$name.'()', E_USER_ERROR);
    }
} 