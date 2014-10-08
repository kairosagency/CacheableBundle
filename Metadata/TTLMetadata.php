<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 30/09/2014
 * Time: 16:15
 */
namespace Kairos\CacheBundle\Metadata;

use Metadata\MethodMetadata;

class TTLMetadata extends MethodMetadata
{
    /**
     * @var int $ttl
     */
    public $ttl;


    public function serialize()
    {
        return serialize(array(
                $this->class,
                $this->name,
                $this->ttl,
            ));
    }

    public function unserialize($str)
    {
        list($this->class, $this->name, $this->ttl) = unserialize($str);

        $this->reflection = new \ReflectionMethod($this->class, $this->name);
        $this->reflection->setAccessible(true);
    }
}
