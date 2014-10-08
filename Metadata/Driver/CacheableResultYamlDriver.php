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
use Metadata\Driver\AbstractFileDriver;
use Symfony\Component\Yaml\Yaml;
use Kairos\CacheBundle\Lib\Utils;


class CacheableResultYamlDriver extends AbstractFileDriver {

    protected function loadMetadataFromFile(\ReflectionClass $class, $file)
    {

        $classMetadata = new CacheProviderMetadata($class->getName());
        $data = Yaml::parse($file);

        if(isset($data[$class->getName()]) && isset($data[$class->getName()]['methods']) && $methods = $data[$class->getName()]['methods'])
        {
            $classMetadata->cacheProvider = isset($data[$class->getName()]['cache_provider'])?  Utils::normalizeServiceId($data[$class->getName()]['cache_provider']) : null;

            foreach ($methods as $methodName => $value) {
                $methodMetadata = new TTLMetadata($class->getName(), $methodName);
                $methodMetadata->ttl = isset($value['ttl'])?  $value['ttl'] : null;
                $classMetadata->addMethodMetadata($methodMetadata);
            }
        }

        return $classMetadata;
    }

    protected function getExtension()
    {
        return 'yml';
    }
} 