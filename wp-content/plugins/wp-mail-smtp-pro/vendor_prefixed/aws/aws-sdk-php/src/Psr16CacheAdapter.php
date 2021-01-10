<?php

namespace WPMailSMTP\Vendor\Aws;

use WPMailSMTP\Vendor\Psr\SimpleCache\CacheInterface as SimpleCacheInterface;
class Psr16CacheAdapter implements \WPMailSMTP\Vendor\Aws\CacheInterface
{
    /** @var SimpleCacheInterface */
    private $cache;
    public function __construct(\WPMailSMTP\Vendor\Psr\SimpleCache\CacheInterface $cache)
    {
        $this->cache = $cache;
    }
    public function get($key)
    {
        return $this->cache->get($key);
    }
    public function set($key, $value, $ttl = 0)
    {
        $this->cache->set($key, $value, $ttl);
    }
    public function remove($key)
    {
        $this->cache->delete($key);
    }
}
