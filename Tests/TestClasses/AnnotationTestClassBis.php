<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 01/10/2014
 * Time: 10:58
 */

namespace Kairos\CacheableBundle\Tests\TestClasses;
use Kairos\CacheableBundle\Annotation as KairosCache;


class AnnotationTestClassBis {

    public $name;

    /**
     * @KairosCache\TTL(1800)
     */
    public function coucou()
    {
        return "coucou";
    }

    /**
     * @KairosCache\TTL(1801)
     */
    public function coucou2()
    {
        return "coucou2";
    }
}
