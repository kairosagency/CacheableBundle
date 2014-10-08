<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 30/09/2014
 * Time: 16:25
 */

namespace Kairos\CacheBundle\Metadata\Driver;

use Kairos\CacheBundle\Metadata\CacheProviderMetadata;
use Kairos\CacheBundle\Metadata\TTLMetadata;
use Metadata\Driver\DriverInterface;
use Doctrine\Common\Annotations\Reader;
use Kairos\CacheBundle\Lib\Utils;

class CacheableResultAnnotationDriver implements DriverInterface {
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $classMetadata = new CacheProviderMetadata($class->getName());

        $classAnnotation = $this->reader->getClassAnnotation(
            $class,
            'Kairos\\CacheBundle\\Annotation\\CacheProvider'
        );

        if (null !== $classAnnotation) {
            // a "@DefaultValue" annotation was found
            $classMetadata->cacheProvider = Utils::normalizeServiceId($classAnnotation->cacheProvider);
        }

        foreach ($class->getMethods() as $method) {
            $metadata = new TTLMetadata($class->getName(), $method->getName());

            $annotation = $this->reader->getMethodAnnotation(
                $method,
                'Kairos\\CacheBundle\\Annotation\\TTL'
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