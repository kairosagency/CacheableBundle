<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Kairos\CacheBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Kairos\CacheBundle\Lib\Utils;

/**
 * Cache Bundle Extension
 *
 * @author Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @author Danilo Cabello <danilo.cabello@gmail.com>
 */
class KairosCacheExtension extends Extension
{

    /**
     * @var ContainerBuilder
     */
    protected $container;


    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->container = $container;
        $configuration = new Configuration();
        $rootConfig    = $this->processConfiguration($configuration, $configs);

        $locator = new FileLocator(__DIR__ . '/../Resources/config/');
        $loader  = new XmlFileLoader($container, $locator);

        // remap config parameters to bundle parameters
        $this->remapParametersNamespaces($rootConfig, array(
                'cacheable_default'          => array(
                    'ttl' => 'kairos_cache.cacheable_default.default_ttl',
                    'cache_dir'         =>  'kairos_cache.cacheable_default.cache_dir',
                ),
                'metadata_default'   => array(
                    'cache_dir'         =>  'kairos_cache.metadata_default.cache_dir',
                )
            )
        );

        $loader->load('metadataFactory.xml');
        $this->loadMetadataCacheService($rootConfig, $container);
        $this->loadResultCacheService($rootConfig, $container);
        $this->getYamlDirectories($rootConfig, $container);
        if($rootConfig['debug']) {
            $container->getDefinition('kairos_cache.metadata_factory')->removeMethodCall('setCache');
        }
    }


    protected function loadMetadataCacheService(array $rootConfig, ContainerBuilder $container)
    {
        if(isset($rootConfig['metadata_default']['cache_provider']) && $serviceId = Utils::normalizeServiceId($rootConfig['metadata_default']['cache_provider'])) {

            $doctrineCacheAdpater = new Definition('Metadata\\Cache\\DoctrineCacheAdapter',
                array(
                    'metadata',
                    new Reference($serviceId)
                )
            );
            $container->setDefinition('kairos_cache.default_metadata_cache', $doctrineCacheAdpater);
        }
        else {
            $defaultCacheDefinition = new Definition('%kairos_cache.filesystem.class%',  array($container->getParameter('kairos_cache.metadata_default.cache_dir')));
            $doctrineCacheAdpater = new Definition('Metadata\\Cache\\DoctrineCacheAdapter',  array('metadata', $defaultCacheDefinition));
            $container->setDefinition('kairos_cache.default_metadata_cache', $doctrineCacheAdpater);
        }

    }

    protected function loadResultCacheService(array $rootConfig, ContainerBuilder $container)
    {
        if(isset($rootConfig['metadata_default']['cache_provider']) && $serviceId = Utils::normalizeServiceId($rootConfig['metadata_default']['cache_provider'])) {
            $container->setAlias('kairos_cache.default_cache', $serviceId);
        }
        else {
            $defaultCacheDefinition = new Definition('%kairos_cache.filesystem.class%',  array($container->getParameter('kairos_cache.cacheable_default.cache_dir')));
            $container->setDefinition('kairos_cache.default_cache', $defaultCacheDefinition);
        }
    }




    protected function getYamlDirectories(array $rootConfig)
    {
        $bundles = $this->container->getParameter('kernel.bundles');

        // directories
        $directories = array();
        /*if ($rootConfig['auto_detection']) {
            foreach ($bundles as $name => $class) {
                $ref = new \ReflectionClass($class);

                $directories[$ref->getNamespaceName()] = dirname($ref->getFileName()).'/Resources/config/cacheable';
            }
        }*/

        foreach ($rootConfig['directories'] as $directory) {
            $directory['path'] = rtrim(str_replace('\\', '/', $directory['path']), '/');

            if ('@' === $directory['path'][0]) {
                $bundleName = substr($directory['path'], 1, strpos($directory['path'], '/') - 1);

                if (!isset($bundles[$bundleName])) {
                    throw new \Exception(sprintf('The bundle "%s" has not been registered with AppKernel. Available bundles: %s', $bundleName, implode(', ', array_keys($bundles))));
                }

                $ref = new \ReflectionClass($bundles[$bundleName]);
                $directory['path'] = dirname($ref->getFileName()).substr($directory['path'], strlen('@'.$bundleName));
            }

            $directories[rtrim($directory['namespace_prefix'], '\\')] = rtrim($directory['path'], '\\/');
        }

        $this->container
            ->getDefinition('kairos_cache.metadata.file_locator')
            ->replaceArgument(0, $directories);

    }

    /******** util functions ********/

    /**
     * @param array $config
     * @param array $map
     */
    protected function remapParameters(array $config, array $map)
    {
        foreach ($map as $name => $paramName) {
            if (array_key_exists($name, $config)) {
                $this->container->setParameter($paramName, $config[$name]);
            }
        }
    }

    /**
     * @param array $config
     * @param array $namespaces
     */
    protected function remapParametersNamespaces(array $config, array $namespaces)
    {
        foreach ($namespaces as $ns => $map) {
            if ($ns) {
                if (!array_key_exists($ns, $config)) {
                    continue;
                }
                $namespaceConfig = $config[$ns];
            } else {
                $namespaceConfig = $config;
            }
            if (is_array($map)) {
                $this->remapParameters($namespaceConfig, $map);
            } else {
                foreach ($namespaceConfig as $name => $value) {
                    $this->container->setParameter(sprintf($map, $name), $value);
                }
            }
        }
    }
}
