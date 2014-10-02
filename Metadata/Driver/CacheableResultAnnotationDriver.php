<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 30/09/2014
 * Time: 16:25
 */

namespace Kairos\CacheBundle\Metadata\Driver;

use Metadata\Driver\DriverInterface;
use Metadata\MergeableClassMetadata;
use Doctrine\Common\Annotations\Reader;
use Kairos\CacheBundle\Metadata\CacheableResultMetadata;

class CacheableResultAnnotationDriver implements DriverInterface {
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $classMetadata = new MergeableClassMetadata($class->getName());


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