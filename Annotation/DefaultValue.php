<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 01/10/2014
 * Time: 11:22
 */

namespace Snc\RedisBundle\Annotation;

/**
 * @Annotation
 */
class DefaultValue
{
    public $value;

    public function __construct(array $data)
    {
        $this->value = $data['value'];
    }
}