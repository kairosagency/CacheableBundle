<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 30/09/2014
 * Time: 16:25
 */

namespace Kairos\CacheableBundle\Metadata\Driver;

use Kairos\CacheableBundle\Annotation\CacheProvider;
use Kairos\CacheableBundle\Metadata\CacheProviderMetadata;
use Kairos\CacheableBundle\Metadata\TTLMetadata;
use Metadata\Driver\DriverInterface;
use Doctrine\Common\Annotations\Reader;
use Kairos\CacheableBundle\Lib\Utils;
use Symfony\Component\DependencyInjection\Reference;

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
            'Kairos\\CacheableBundle\\Annotation\\CacheProvider'
        );

        if (null !== $classAnnotation) {
            // a "@DefaultValue" annotation was found
            $classMetadata->cacheProvider = !is_null($classAnnotation->cacheProvider) ? new Reference(Utils::normalizeServiceId($classAnnotation->cacheProvider)) : null;
        }

        foreach ($class->getMethods() as $method) {
            $metadata = new TTLMetadata($class->getName(), $method->getName());

            $annotation = $this->reader->getMethodAnnotation(
                $method,
                'Kairos\\CacheableBundle\\Annotation\\TTL'
            );

            if (null !== $annotation) {
                // a "@DefaultValue" annotation was found
                $metadata->ttl = $annotation->ttl;
                $classMetadata->addMethodMetadata($metadata);
            }
        }

        return $classMetadata;
    }
}
