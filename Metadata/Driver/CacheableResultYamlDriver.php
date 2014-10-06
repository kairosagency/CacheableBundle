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

        foreach ($data as $methodName => $value) {
            $methodMetadata = new CacheableResultMetadata($class->getName(), $methodName);
            $methodMetadata->defaultValue = $value;

            $classMetadata->addMethodMetadata($methodMetadata);
        }

        return $classMetadata;
    }

    protected function getExtension()
    {
        return 'yml';
    }

} 