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
use Doctrine\Common\Cache\PhpFileCache;

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
    public static function parameterValues()
    {
        /*return array(
            array('kairos_cache.php_file.class', 'Doctrine\Common\Cache\PhpFileCache'),
            array('snc_redis.client_options.class', 'Predis\Option\ClientOptions'),
            array('snc_redis.connection_parameters.class', 'Predis\Connection\ConnectionParameters'),
            array('snc_redis.connection_factory.class', 'Kairos\CacheBundle\Client\Predis\Connection\ConnectionFactory'),
            array('snc_redis.connection_wrapper.class', 'Kairos\CacheBundle\Client\Predis\Connection\ConnectionWrapper'),
            array('snc_redis.logger.class', 'Kairos\CacheBundle\Logger\RedisLogger'),
            array('snc_redis.data_collector.class', 'Kairos\CacheBundle\DataCollector\RedisDataCollector'),
            array('snc_redis.doctrine_cache.class', 'Kairos\CacheBundle\Doctrine\Cache\RedisCache'),
            array('snc_redis.monolog_handler.class', 'Monolog\Handler\RedisHandler'),
            array('snc_redis.swiftmailer_spool.class', 'Kairos\CacheBundle\SwiftMailer\RedisSpool'),
        );*/
    }


    /**
     * @param string $name     Name
     * @param string $expected Expected value
     *
     */
    /*public function testDefaultParameterConfigLoad()
    {
        $extension = new KairosCacheExtension();
        $config = $this->parseYaml($this->getMinimalYamlConfig());
        $extension->load(array($config), $container = $this->getContainer());
        //var_dump($container);
        //$this->assertEquals($expected, $container->getParameter($name));
    }*/

    /**
     * @param string $name     Name
     * @param string $expected Expected value
     *
     */
    public function testFullParameterConfigLoad()
    {




        $container = $this->getContainer();

        $container
            ->getDefinition('kairos_cache.metadata.file_locator')
            ->replaceArgument(0, array('Kairos/CacheBundle/Tests/TestClasses/YamlTestClass' => __DIR__.'/../Ressources/config/cache'));

        $metadataFactory = $container->get('kairos_cache.metadata_factory');


        //var_dump($container);
        var_dump($metadataFactory->getMetadataForClass('Kairos\\CacheBundle\\Tests\\TestClasses\\AnnotationTestClass'));
        var_dump($metadataFactory->getMetadataForClass('Kairos\\CacheBundle\\Tests\\TestClasses\\YamlTestClass'));


        //$this->assertEquals($expected, $container->getParameter($name));
    }

    private function parseYaml($yaml)
    {
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    private function getMinimalYamlConfig()
    {
        return <<<'EOF'
kairos_cache:
    cacheable_default:
        cache_dir: %kernel.cache_dir%/kairos
    metadata_default:
        cache_dir: %kernel.cache_dir%/kairos
EOF;
    }

    private function getYamlConfig()
    {
        return <<<'EOF'
kairos_cache:
    cacheable_default:
        ttl: 1800
        cache_dir: %kernel.cache_dir%/kairos

    metadata_default:
        cache_dir: %kernel.cache_dir%/kairos

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
    cacheable_default:
        ttl: 1800
        cache_dir: %kernel.cache_dir%/kairos
        cache_provider: @test_cache_provider

    metadata_default:
        cache_provider: @test_cache_provider
        cache_dir: %kernel.cache_dir%/kairos

    directories:
        KairosCacheBundle:
            namespace_prefix: Kairos\CacheBundle
            path: "@KairosCacheBundle/Tests/Resources/config/cache"
EOF;
    }


    private function getContainer(array $config = null)
    {
        AnnotationRegistry::registerFile(__DIR__.'/../../Annotation/CacheableResult.php');
        $container = new ContainerBuilder(new ParameterBag(array(
            'kernel.debug'       => false,
            'kernel.bundles'     => array('KairosCacheBundle' => 'Kairos\CacheBundle\KairosCacheBundle'),
            'kernel.cache_dir'   => __DIR__.'/../cache/',
            'kernel.environment' => 'test',
            'kernel.root_dir'    => __DIR__.'/../../' // src dir
        )));

        $container->set('annotation_reader', new AnnotationReader());
        $container->set('@test_cache_provider', new PhpFileCache(__DIR__.'/../cache'));

        $extension = new KairosCacheExtension();
        $container->registerExtension($extension);

        if(is_null($config))
            $config = $this->parseYaml($this->getMinimalYamlConfig());

        $extension->load($config, $container);
        $container->compile();
        return $container;
    }
}
