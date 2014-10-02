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
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $rootConfig    = $this->processConfiguration($configuration, $configs);

        $locator = new FileLocator(__DIR__ . '/../Resources/config/');
        $loader  = new XmlFileLoader($container, $locator);

        if(isset($rootConfig['ttl']))
            $container->setParameter('kairos_cache.default_ttl',  $rootConfig['ttl']);


        $loader->load('services.xml');
        $this->processYamlFiles($rootConfig, $container);
        $this->loadMetadataCacheProvider($rootConfig, $container);
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function loadMetadataCacheProvider(array $config, ContainerBuilder $container)
    {
        if(isset($config['default_cache']['cache_provider']) && $cacheProvider = $config['default_cache']['cache_provider']) {
            $container->setAlias('kairos_cache.default_cache', $cacheProvider);
        }
        else {
            $parameters = array($config['default_cache']['cache_dir']);
            $defaultCacheDefinition = new Definition('%kairos_cache.php_file.class%', $parameters);
            $container->setDefinition('kairos_cache.default_cache', $defaultCacheDefinition);
        }
    }

    protected function processYamlFiles(array $rootConfig, ContainerBuilder $container)
    {
        $directories = array();
        foreach ($rootConfig['directories'] as $directory) {
            $directories[$directory['namespace_prefix']] = $directory['path'];
        }

        $container
            ->getDefinition('kairos_cache.metadata.file_locator')
            ->replaceArgument(0, $directories)
        ;

    }

}
