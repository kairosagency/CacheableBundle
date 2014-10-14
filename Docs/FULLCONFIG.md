# CacheableBundle #

## Full bundle configuration ##

```
kairos_cache:
    debug: true|false               # disable metadata cache (annotation and yaml files are put in cache)
    cacheable_default:
        ttl: integer                # default ttl if no ttl is set
        cache_dir: string           # cache directory for default cache provider
        cache_provider: @service    # you can use redis or apc cache using Doctrine\CacheBuncle or Doctrine\Cache lib

    metadata_default:
        cache_provider: @service    # you can use redis or apc cache using Doctrine\CacheBuncle or Doctrine\Cache lib
        cache_dir: string           # cache directory for default metadata cache provider

    directories:
        CompanyBundleName:
            namespace_prefix: Company\BundleName
            path: "@CompanyBundleName/Path/To/Yaml/Dir"
```


## Use another cache service ##

You can use other cache service such as redis, apc using DoctrineCacheBundle (or doctrine cache lib).

With DoctrineCacheBundle, just setup a new cache provider :

```
doctrine_cache:
    providers:
        my_apc_metadata_cache:
            type: apc
            namespace: metadata_cache_ns
        my_apc_query_cache:
            namespace: query_cache_ns
            apc: ~
```

Then you can use "doctrine_cache.providers.my_apc_cache" as a cache provider in your cache setup.