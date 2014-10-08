<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 30/09/2014
 * Time: 16:15
 */
namespace Kairos\CacheBundle\Metadata;

use Metadata\MergeableClassMetadata;

class CacheProviderMetadata extends MergeableClassMetadata
{
    /**
     * @var string $cacheProvider
     */
    public $cacheProvider;


    public function serialize()
    {
        return serialize(
            array(
                $this->name,
                $this->cacheProvider,
            )
        );
    }

    public function unserialize($str)
    {
        list($this->name, $this->cacheProvider) = unserialize($str);

        $this->reflection = new \ReflectionClass($this->name);
    }
}