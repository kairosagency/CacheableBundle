<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 02/10/2014
 * Time: 10:12
 */

namespace Kairos\CacheBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class KairosCacheExtension extends Extension {


    public function load(array $configs, ContainerBuilder $container)
    {

        $cacheDirectory = '%kernel.cache_dir%/kairos_cache';
        $cacheDirectory = $container->getParameterBag()->resolveValue($cacheDirectory);
        if (!is_dir($cacheDirectory)) {
            mkdir($cacheDirectory, 0777, true);
        }

        // the cache directory should be the first argument of the cache service
        $container
            ->getDefinition('kairos.cacheBundle.metadata.cache')
            ->replaceArgument(0, $cacheDirectory)
        ;
    }
} 