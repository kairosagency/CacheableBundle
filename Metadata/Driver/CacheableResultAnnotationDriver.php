<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 30/09/2014
 * Time: 16:25
 */

namespace Snc\RedisBundle\Metadata\Driver;

use Metadata\Driver\DriverInterface;
use Metadata\MergeableClassMetadata;
use Doctrine\Common\Annotations\Reader;
use Snc\RedisBundle\Metadata\CacheableResultMetadata;
use Snc\RedisBundle\Metadata\PropertyMetadata;

class CacheableResultAnnotationDriver implements DriverInterface {
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $classMetadata = new MergeableClassMetadata($class->getName());

        foreach ($class->getProperties() as $reflectionProperty) {
            $propertyMetadata = new PropertyMetadata($class->getName(), $reflectionProperty->getName());
            var_dump($propertyMetadata);
            $annotation = $this->reader->getPropertyAnnotation(
                $reflectionProperty,
                'Snc\\RedisBundle\\Annotation\\CacheableResult'
            );

            if (null !== $annotation) {
                // a "@DefaultValue" annotation was found
                $propertyMetadata->ttl = $annotation->ttl;
            }

            $classMetadata->addPropertyMetadata($propertyMetadata);
        }


        var_dump($class->getMethods());

        foreach ($class->getMethods() as $method) {
            $metadata = new CacheableResultMetadata($class->getName(), $method->getName());

            $annotation = $this->reader->getMethodAnnotation(
                $method,
                'Snc\\RedisBundle\\Annotation\\CacheableResult'
            );

            if (null !== $annotation) {
                // a "@DefaultValue" annotation was found
                $metadata->ttl = $annotation->ttl;
            }

            $classMetadata->addMethodMetadata($metadata);
        }

        return $classMetadata;
    }
} 