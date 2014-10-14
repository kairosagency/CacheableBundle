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

namespace Kairos\CacheableBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;


class DefaultCacheCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        $this->loadCachedServices($container);
    }


    /**
     * @param ContainerBuilder $container
     */
    protected function loadCachedServices(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds(
            'kairos_cacheable.cacheable'
        );

        $metadataFactory = $container->get('kairos_cacheable.metadata_factory');

        foreach ($taggedServices as $id => $attributes) {
            $serviceDefinition = $container->getDefinition($id);
            $className = $serviceDefinition->getClass();

            $defaultTTL = $container->hasParameter('kairos_cacheable.cacheable_default.default_ttl') ? $container->getParameter('kairos_cacheable.cacheable_default.default_ttl') : null ;

            $metadata = $metadataFactory->getMetadataForClass($className);

            if(is_null($metadata->cacheProvider)) {
                $cache = new Reference('kairos_cacheable.default_cache');
            }
            else {
                $cache = $metadata->cacheProvider;
            }

            $definition = new Definition('Kairos\CacheableBundle\Service\CacheableProxyService',
                array(
                    $metadata,
                    $cache,
                    $serviceDefinition,
                    $defaultTTL
                )
            );

            //$definition->addMethodCall('setMetadata', $metadata);
            $container->setDefinition($id.'.cacheable', $definition);
        }
    }
}
