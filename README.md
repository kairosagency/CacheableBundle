# CacheBundle #

[![Build Status](https://travis-ci.org/t0k4rt/CacheBundle.svg?branch=master)](https://travis-ci.org/t0k4rt/CacheBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/t0k4rt/CacheBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/t0k4rt/CacheBundle/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/t0k4rt/CacheBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/t0k4rt/CacheBundle/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/17784ecd-e996-429d-8967-f46360a51239/mini.png)](https://insight.sensiolabs.com/projects/17784ecd-e996-429d-8967-f46360a51239)

## About ##

Kairos CacheBundle provides easy result caching with annotation or yaml configuration.
It is aimed at easing the caching of results from any methods in your code.

You can cache api call results, doctrine results or any methods that sends you some data.

## Documentation ##

### Bundle setup ###

Install the bundle via composer :

```json
"require": {
    ...
    "kairos/cachebundle": "*",
    ...
}
```

Register your bundle in your AppKernel.php

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            ...
            new Kairos\CacheBundle\KairosCacheBundle(),
            ...
        );
    ....
    }
}
```


Minimal/optional config for your config.yml :

```
kairos_cache: ~
```


### Usage ###

#### 1/ Create a tagged service ####

Your Class must be a service tagged withe the name "kairos_cache.cacheable" :

```
services:
    your_cacheable_Service:
        class: \Cacheable_class
        tags:
            -  { name: kairos_cache.cacheable }
```

#### 2/ Setup cache parameters ####

* With annotation :

Don't forget the "use Kairos\CacheBundle\Annotation as KairosCache" statement.

```php

use Kairos\CacheBundle\Annotation as KairosCache;

/**
 * If you want to use a custom cache provider setup one, otherwise the bundle use a default filesystem
 * cache to store method results
 * @KairosCache\CacheProvider("@custom_Cache_provider")
 */
class YourCacheableClass
{
    /**
     * @KairosCache\TTL(1800)
     */
    public function cacheableMethod()
    {
        return "cacheable result";
    }
}
```

* With yaml :


First create your yaml config files :

Nota bene : the filename must be Fully.Qualified.Namespace.ClassName.yml

```
YourCompany\Bundle\Fully\Qualified\Namespace\ClassName:
    cache_provider: @custom_cache_provider
    methods:
        methode_to_cache:
            ttl: 3600
```

Then register the directories where to find the yaml config files in your config.yml :

```
kairos_cache:
    directories:
        YourCompanyBundle:
            namespace_prefix: YourCompany\Bundle
            path: "@YourCompanyBundle/Resources/config/cacheable"
```

#### 3/ Step three ####

No step three


## More dicumentation ##

[More](./blob/master/Docs/FULLCONFIG.md)

## License ##

[Mozilla Public License 2.0](https://www.mozilla.org/MPL/2.0/)
