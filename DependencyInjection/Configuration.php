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

namespace Kairos\CacheableBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Cache Bundle Configuration
 *
 * @author Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kairos_cache');

        $rootNode
            ->children()
                ->scalarNode('debug')->defaultFalse()->end()
                ->scalarNode('auto_detection')->defaultFalse()->end()
            ->end()
        ;

        $this->addMetadataSection($rootNode);
        $this->addCacheSection($rootNode);
        $this->addDirectoriesSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $builder
     * @return ArrayNodeDefinition
     */
    private function addMetadataSection(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->arrayNode('cacheable_default')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('cache_provider')->end()
                        ->integerNode('ttl')->min(0)->end()
                        ->scalarNode('cache_dir')->defaultValue('%kernel.cache_dir%/kairosCache/resultCache')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $builder;
    }

    /**
     * @param ArrayNodeDefinition $builder
     * @return ArrayNodeDefinition
     */
    private function addCacheSection(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->arrayNode('metadata_default')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('cache_provider')->end()
                        ->scalarNode('cache_dir')->defaultValue('%kernel.cache_dir%/kairosCache/metadata')->end()
                    ->end()
                ->end()
            ->end();

        return $builder;
    }


    /**
     * @param ArrayNodeDefinition $builder
     * @return ArrayNodeDefinition
     */
    private function addDirectoriesSection(ArrayNodeDefinition $builder)
    {
        $builder
            ->fixXmlConfig('directory', 'directories')
            ->children()
                ->arrayNode('directories')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('path')->isRequired()->end()
                            ->scalarNode('namespace_prefix')->defaultValue('')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $builder;
    }
}
