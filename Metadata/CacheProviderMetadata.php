<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 30/09/2014
 * Time: 16:15
 */
namespace Kairos\CacheableBundle\Metadata;

use Metadata\MergeableClassMetadata;
use Symfony\Component\DependencyInjection\Reference;

class CacheProviderMetadata extends MergeableClassMetadata
{
    /**
     * @var Reference $cacheProvider
     */
    public $cacheProvider;


    public function serialize()
    {
        $toSerialize = array(
            $this->name,
            $this->methodMetadata,
            $this->propertyMetadata,
            $this->fileResources,
            $this->createdAt,
            array()
        );
        if(!is_null($this->cacheProvider)) {
            $toSerialize[5] = array(
                $this->cacheProvider->__toString(),
                $this->cacheProvider->getInvalidBehavior(),
                $this->cacheProvider->isStrict(),
            );
        }
        return serialize($toSerialize);
    }

    public function unserialize($str)
    {
        list(
            $this->name,
            $this->methodMetadata,
            $this->propertyMetadata,
            $this->fileResources,
            $this->createdAt,
            $cacheProvider
            ) = unserialize($str);
        if(!empty($cacheProvider)) {
            $this->cacheProvider = new Reference($cacheProvider[0],$cacheProvider[1],$cacheProvider[2]);
        }
        $this->reflection = new \ReflectionClass($this->name);
    }
}
