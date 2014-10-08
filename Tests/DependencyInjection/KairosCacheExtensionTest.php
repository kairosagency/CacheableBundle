<?php

/*
 * This file is part of the SncRedisBundle package.
 *
 * (c) Henrik Westphal <henrik.westphal@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kairos\CacheBundle\Tests\DependencyInjection;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Cache\PhpFileCache;

use Kairos\CacheBundle\DependencyInjection\Compiler\DefaultCacheCompilerPass;
use Kairos\CacheBundle\DependencyInjection\KairosCacheExtension;
use Kairos\CacheBundle\KairosCacheBundle;
use Kairos\CacheBundle\Metadata\CacheableResultMetadata;
use Symfony\Bundle\FrameworkBundle\Tests\Functional\app\AppKernel;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Parser;

/**
 * SncRedisExtensionTest
 */
class KairosCacheExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @static
     *
     * @return array
     */
    public static function defaultParameterValues()
    {
        return array(
            array('kairos_cache.filesystem.class', 'Doctrine\Common\Cache\FilesystemCache'),
            array('kairos_cache.metadata_factory.class', 'Metadata\MetadataFactory'),
            array('kairos_cache.metadata.driver_chain.class', 'Metadata\Driver\DriverChain'),
            array('kairos_cache.metadata.file_locator_class', 'Metadata\Driver\FileLocator'),
            array('kairos_cache.metadata.annotation_driver.class', 'Kairos\CacheBundle\Metadata\Driver\CacheableResultAnnotationDriver'),
            array('kairos_cache.metadata.yaml_driver.class', 'Kairos\CacheBundle\Metadata\Driver\CacheableResultYamlDriver'),
            array('kairos_cache.metadata_default.cache_dir', __DIR__.'/../cache//kairosCache/metadata'),
            array('kairos_cache.cacheable_default.cache_dir', __DIR__.'/../cache//kairosCache/resultCache'),
        );
    }


    /**
     * @param string $name     Name
     * @param string $expected Expected value
     *
     * @dataProvider defaultParameterValues
     */
    public function testDefaultParameterConfigLoad($name, $expected)
    {
        $container = $this->getContainer($this->parseYaml($this->getMinimalYamlConfig()));
        $this->assertEquals($expected, $container->getParameter($name));
    }

    /**
     *
     */
    public function testDefaultMetadataCache()
    {
        $container = $this->getContainer($this->parseYaml($this->getMinimalYamlConfig()));

        $defaultMetadataCache = $container->get('kairos_cache.default_metadata_cache');
        $this->assertInstanceOf('Metadata\Cache\DoctrineCacheAdapter', $defaultMetadataCache);
    }

    /**
     *
     */
    public function testDefaultCacheableCache()
    {
        $container = $this->getContainer($this->parseYaml($this->getMinimalYamlConfig()));

        $defaultMetadataCache = $container->get('kairos_cache.default_cache');
        $this->assertInstanceOf('Doctrine\Common\Cache\Cache', $defaultMetadataCache);
    }


    /**
     * @static
     *
     * @return array
     */
    public static function ClassicParameterValues()
    {
        return array(
            array('kairos_cache.filesystem.class', 'Doctrine\Common\Cache\FilesystemCache'),
            array('kairos_cache.metadata_factory.class', 'Metadata\MetadataFactory'),
            array('kairos_cache.metadata.driver_chain.class', 'Metadata\Driver\DriverChain'),
            array('kairos_cache.metadata.file_locator_class', 'Metadata\Driver\FileLocator'),
            array('kairos_cache.metadata.annotation_driver.class', 'Kairos\CacheBundle\Metadata\Driver\CacheableResultAnnotationDriver'),
            array('kairos_cache.metadata.yaml_driver.class', 'Kairos\CacheBundle\Metadata\Driver\CacheableResultYamlDriver'),
            array('kairos_cache.metadata_default.cache_dir', __DIR__.'/../cache//kairos2'),
            array('kairos_cache.cacheable_default.cache_dir', __DIR__.'/../cache//kairos1'),
        );
    }


    /**
     * @param string $name     Name
     * @param string $expected Expected value
     *
     * @dataProvider ClassicParameterValues
     */
    public function testClassicParameterConfigLoad($name, $expected)
    {
        $container = $this->getContainer($this->parseYaml($this->getClassicYamlConfig()));
        $this->assertEquals($expected, $container->getParameter($name));
    }


    /**
     * @static
     *
     * @return array
     */
    public static function testClassMetadata()
    {
        return array(
            array('Kairos\\CacheBundle\\Tests\\TestClasses\\AnnotationTestClass',
                array(
                    "cacheProvider" => "doctrine.test_cache",
                    "methodMetadata" => array(
                        "coucou2" => array("ttl" => 1801),
                        "coucou" => array("ttl" => 1800)
                    )
                )
            ),
            array('Kairos\\CacheBundle\\Tests\\TestClasses\\YamlTestClass',
                array(
                    "cacheProvider" => "doctrine.test_cache",
                    "methodMetadata" => array(
                        "coucou2" => array("ttl" => 3601),
                        "coucou" => array("ttl" => 3600)
                    )
                )
            ),
            array('Kairos\\CacheBundle\\Tests\\TestClasses\\AnnotationTestClassBis',
                array(
                    "cacheProvider" => null,
                    "methodMetadata" => array(
                        "coucou2" => array("ttl" => 1801),
                        "coucou" => array("ttl" => 1800)
                    )
                )
            ),
            array('Kairos\\CacheBundle\\Tests\\TestClasses\\YamlTestClassBis',
                array(
                    "cacheProvider" => null,
                    "methodMetadata" => array(
                        "coucou2" => array("ttl" => 3601),
                        "coucou" => array("ttl" => 3600)
                    )
                )
            ),
        );
    }

    /**
     * @param string $name     Name
     * @param array $expected Expected value
     *
     * @dataProvider testClassMetadata
     */
    public function testMetadataLoad($class, $expected)
    {
        $container = $this->getContainer($this->parseYaml($this->getClassicYamlConfig()));

        $metadataFactory = $container->get('kairos_cache.metadata_factory');
        $metadata = $metadataFactory->getMetadataForClass($class);

        $this->assertEquals($expected["cacheProvider"], $metadata->cacheProvider);
        foreach($expected["methodMetadata"] AS $key => $result) {
            if(isset($metadata->methodMetadata[$key])) {
                $methodMetadata = $metadata->methodMetadata[$key];
                $this->assertEquals($result["ttl"], $methodMetadata->ttl);
            }
        }
    }

    /**
     * @param string $name     Name
     * @param array $expected Expected value
     *
     * @dataProvider testClassMetadata
     */
    public function testFullParameterMetadataLoad($class, $expected)
    {
        $container = $this->getContainer($this->parseYaml($this->getFullYamlConfig()));

        $metadataFactory = $container->get('kairos_cache.metadata_factory');
        $metadata = $metadataFactory->getMetadataForClass($class);

        $this->assertEquals($expected["cacheProvider"], $metadata->cacheProvider);
        foreach($expected["methodMetadata"] AS $key => $result) {
            if(isset($metadata->methodMetadata[$key])) {
                $methodMetadata = $metadata->methodMetadata[$key];
                $this->assertEquals($result["ttl"], $methodMetadata->ttl);
            }
        }
    }


    /**
     * @static
     *
     * @return array
     */
    public static function testClassicClassServices()
    {
        return array(
            array('Kairos\\CacheBundle\\Tests\\TestClasses\\AnnotationTestClass',
                array(
                    "cacheProvider" => "doctrine.test_cache"
                )
            ),
            array('Kairos\\CacheBundle\\Tests\\TestClasses\\YamlTestClass',
                array(
                    "cacheProvider" => "doctrine.test_cache"
                )
            ),
            array('Kairos\\CacheBundle\\Tests\\TestClasses\\AnnotationTestClassBis',
                array(
                    "cacheProvider" => "kairos_cache.default_cache"
                )
            ),
            array('Kairos\\CacheBundle\\Tests\\TestClasses\\YamlTestClassBis',
                array(
                    "cacheProvider" => "kairos_cache.default_cache"
                )
            ),
        );
    }

    /**
     * @param $class
     * @param $expected
     *
     * @dataProvider testClassicClassServices
     */
    public function testClassicServiceBuilderLoad($class, $expected)
    {
        $container = $this->getUnbuiltContainer($this->parseYaml($this->getClassicYamlConfig()));

        $cacheableService = new Definition($class);
        $cacheableService->addTag('kairos_cache.cacheable');
        $container->setDefinition('kairos_cache.test_cacheable', $cacheableService);

        $container->compile();

        $cacheable = $container->get('kairos_cache.test_cacheable.cacheable');
        $this->assertInstanceOf('Kairos\CacheBundle\Service\CacheableProxyService', $cacheable);
        $this->assertEquals($container->get($expected['cacheProvider']), $cacheable->getCache());
    }


    /**
     * @static
     *
     * @return array
     */
    public static function testFullClassServices()
    {
        return array(
            array('Kairos\\CacheBundle\\Tests\\TestClasses\\AnnotationTestClass',
                array(
                    "cacheProvider" => "doctrine.test_cache"
                )
            ),
            array('Kairos\\CacheBundle\\Tests\\TestClasses\\YamlTestClass',
                array(
                    "cacheProvider" => "doctrine.test_cache"
                )
            ),
            array('Kairos\\CacheBundle\\Tests\\TestClasses\\AnnotationTestClassBis',
                array(
                    "cacheProvider" => "test_cache_provider"
                )
            ),
            array('Kairos\\CacheBundle\\Tests\\TestClasses\\YamlTestClassBis',
                array(
                    "cacheProvider" => "test_cache_provider"
                )
            ),
        );
    }

    /**
     * @param $class
     * @param $expected
     *
     * @dataProvider testFullClassServices
     */
    public function testFullServiceBuilderLoad($class, $expected)
    {
        $container = $this->getUnbuiltContainer($this->parseYaml($this->getFullYamlConfig()));

        $cacheableService = new Definition($class);
        $cacheableService->addTag('kairos_cache.cacheable');
        $container->setDefinition('kairos_cache.test_cacheable', $cacheableService);

        $container->compile();

        $cacheable = $container->get('kairos_cache.test_cacheable.cacheable');
        $this->assertInstanceOf('Kairos\CacheBundle\Service\CacheableProxyService', $cacheable);

        $actualCache = $cacheable->getCache();
        $containerCache = $container->get($expected['cacheProvider']);
        // trick to set namespace to null and avoid error
        $containerCache->setNamespace('toto');

        $this->assertEquals($actualCache, $containerCache);
    }

    private function parseYaml($yaml)
    {
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    private function getMinimalYamlConfig()
    {
        return <<<'EOF'
kairos_cache: ~
EOF;
    }

    private function getClassicYamlConfig()
    {
        return <<<'EOF'
kairos_cache:
    debug: true
    cacheable_default:
        ttl: 1800
        cache_dir: %kernel.cache_dir%/kairos1

    metadata_default:
        cache_dir: %kernel.cache_dir%/kairos2

    directories:
        KairosCacheBundle:
            namespace_prefix: Kairos\CacheBundle
            path: "@KairosCacheBundle/Tests/Resources/config/cache"
EOF;
    }

    private function getFullYamlConfig()
    {
        return <<<'EOF'
kairos_cache:
    debug: true
    cacheable_default:
        ttl: 1800
        cache_dir: %kernel.cache_dir%/kairos11
        cache_provider: @test_cache_provider

    metadata_default:
        cache_provider: @test_cache_provider
        cache_dir: %kernel.cache_dir%/kairos22

    directories:
        KairosCacheBundle:
            namespace_prefix: Kairos\CacheBundle
            path: "@KairosCacheBundle/Tests/Resources/config/cache"
EOF;
    }


    private function getContainer(array $config = null)
    {
        AnnotationRegistry::registerFile(__DIR__.'/../../Annotation/CacheProvider.php');
        AnnotationRegistry::registerFile(__DIR__.'/../../Annotation/TTL.php');
        $container = new ContainerBuilder(new ParameterBag(array(
            'kernel.debug'       => false,
            'kernel.bundles'     => array('KairosCacheBundle' => 'Kairos\CacheBundle\KairosCacheBundle'),
            'kernel.cache_dir'   => __DIR__.'/../cache/',
            'kernel.environment' => 'test',
            'kernel.root_dir'    => __DIR__.'/../../' // src dir
        )));

        $container->set('annotation_reader', new AnnotationReader());
        $defaultCacheDefinition = new Definition('%kairos_cache.filesystem.class%',  array(__DIR__.'/../cache/test_cache_provider'));
        $defaultCacheDefinition->addMethodCall('setNamespace', array('toto'));
        $container->setDefinition('test_cache_provider', $defaultCacheDefinition);
        $container->setDefinition('doctrine.test_cache', $defaultCacheDefinition);

        $extension = new KairosCacheExtension();
        $container->registerExtension($extension);


        if(is_null($config))
            $config = $this->parseYaml($this->getMinimalYamlConfig());

        $extension->load($config, $container);

        $container->addCompilerPass(new DefaultCacheCompilerPass());
        $container->compile();

        return $container;
    }


    private function getUnbuiltContainer(array $config = null)
    {
        AnnotationRegistry::registerFile(__DIR__.'/../../Annotation/CacheProvider.php');
        AnnotationRegistry::registerFile(__DIR__.'/../../Annotation/TTL.php');
        $container = new ContainerBuilder(new ParameterBag(array(
            'kernel.debug'       => false,
            'kernel.bundles'     => array('KairosCacheBundle' => 'Kairos\CacheBundle\KairosCacheBundle'),
            'kernel.cache_dir'   => __DIR__.'/../cache/',
            'kernel.environment' => 'test',
            'kernel.root_dir'    => __DIR__.'/../../' // src dir
        )));

        $container->set('annotation_reader', new AnnotationReader());
        $defaultCacheDefinition = new Definition('%kairos_cache.filesystem.class%',  array(__DIR__.'/../cache/test_cache_provider'));
        $defaultCacheDefinition->addMethodCall('setNamespace', array('toto'));
        $container->setDefinition('test_cache_provider', $defaultCacheDefinition);
        $container->setDefinition('doctrine.test_cache', $defaultCacheDefinition);

        $extension = new KairosCacheExtension();
        $container->registerExtension($extension);


        if(is_null($config))
            $config = $this->parseYaml($this->getMinimalYamlConfig());

        $extension->load($config, $container);
        $container->addCompilerPass(new DefaultCacheCompilerPass());

        return $container;
    }
}
