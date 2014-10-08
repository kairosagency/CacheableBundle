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

namespace Kairos\CacheBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;


class DefaultCacheCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        //$this->loadMetadataCacheProvider($container);
        //$this->loadCacheableCacheProvider($container);
        $this->loadCachedServices($container);
    }


    /**
     * @param ContainerBuilder $container
     */
    protected function loadCachedServices(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds(
            'kairos_cache.cacheable'
        );

        $metadataFactory = $container->get('kairos_cache.metadata_factory');

        foreach ($taggedServices as $id => $attributes) {
            $serviceDefinition = $container->getDefinition($id);
            $className = $serviceDefinition->getClass();

            $defaultTTL = $container->hasParameter('kairos_cache.cacheable_default.default_ttl') ? $container->getParameter('kairos_cache.cacheable_default.default_ttl') : null ;

            $metadata = $metadataFactory->getMetadataForClass($className);
            //if(!is_null($metadata->cacheProvider) && $container->hasDefinition($metadata->cacheProvider)) {
            if(!is_null($metadata->cacheProvider)) {
                $cache = $container->findDefinition($metadata->cacheProvider);
            }
            else {
                $cache = $container->findDefinition('kairos_cache.default_cache');
            }

            $definition = new Definition('Kairos\CacheBundle\Service\CacheableProxyService',
                array(
                    $metadata,
                    $cache,
                    $serviceDefinition,
                    $defaultTTL
                )
            );
            $container->setDefinition($id.'.cacheable', $definition);
        }
    }
}
