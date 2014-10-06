<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 30/09/2014
 * Time: 16:15
 */
namespace Kairos\CacheBundle\Metadata;

use Metadata\MethodMetadata;

class CacheableResultMetadata extends MethodMetadata
{
    /**
     * @var int $ttl
     */
    public $ttl;

    /**
     * @var string $cacheProvider
     */
    public $cacheProvider;


    public function serialize()
    {
        return serialize(array(
                $this->class,
                $this->name,
                $this->ttl,
                $this->cacheProvider,
            ));
    }

    public function unserialize($str)
    {
        list($this->class, $this->name, $this->ttl, $this->cacheProvider) = unserialize($str);

        $this->reflection = new \ReflectionMethod($this->class, $this->name);
        $this->reflection->setAccessible(true);
    }
}