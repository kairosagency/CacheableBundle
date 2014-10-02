<?php

require_once("./vendor/autoload.php");

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\AnnotationReader;
use Kairos\CacheBundle\Tests\Metadata\MetadataTestClass;
use Kairos\CacheBundle\Annotation\DefaultValue;

AnnotationRegistry::registerFile(__DIR__.'/Annotation/DefaultValue.php');
AnnotationRegistry::registerFile(__DIR__.'/Annotation/CacheableResult.php');


/*
$reflClass = new ReflectionClass('Kairos\CacheBundle\Tests\Metadata\MetadataTestClass');
$classAnnotations = $reader->getClassAnnotations($reflClass);
var_dump($reflClass);
var_dump($classAnnotations);*/

$reader = new AnnotationReader();
$annotationDriver = new Kairos\CacheBundle\Metadata\Driver\CacheableResultAnnotationDriver($reader);
$factory = new Metadata\MetadataFactory($annotationDriver);

$md = $annotationDriver->loadMetadataForClass(new ReflectionClass('Kairos\CacheBundle\Tests\Metadata\MetadataTestClass'));
var_dump($md);
