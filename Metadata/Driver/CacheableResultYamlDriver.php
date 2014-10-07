<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 30/09/2014
 * Time: 16:25
 */

namespace Kairos\CacheBundle\Metadata\Driver;

use Metadata\Driver\AbstractFileDriver;
use Metadata\MergeableClassMetadata;
use Kairos\CacheBundle\Metadata\CacheableResultMetadata;
use Symfony\Component\Yaml\Yaml;

class CacheableResultYamlDriver extends AbstractFileDriver {

    protected function loadMetadataFromFile(\ReflectionClass $class, $file)
    {
        $classMetadata = new MergeableClassMetadata($class->getName());
        $data = Yaml::parse($file);

        if(isset($data[$class->getName()]) && isset($data[$class->getName()]['methods']) && $methods = $data[$class->getName()]['methods'])
        {
            foreach ($methods as $methodName => $value) {
                $methodMetadata = new CacheableResultMetadata($class->getName(), $methodName);
                $methodMetadata->ttl = isset($value['ttl'])?  $value['ttl'] : null;
                $methodMetadata->cacheProvider = isset($value['cache_provider'])?  $value['cache_provider'] : null;

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