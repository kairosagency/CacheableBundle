<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 30/09/2014
 * Time: 16:15
 */
namespace Snc\RedisBundle\Metadata;

use Metadata\MethodMetadata;

class CacheableResultMetadata extends MethodMetadata
{
    public $ttl;
}