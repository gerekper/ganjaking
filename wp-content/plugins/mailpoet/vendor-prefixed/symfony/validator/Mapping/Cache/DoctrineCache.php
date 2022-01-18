<?php
namespace MailPoetVendor\Symfony\Component\Validator\Mapping\Cache;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Cache\Cache;
use MailPoetVendor\Symfony\Component\Validator\Mapping\ClassMetadata;
@\trigger_error(\sprintf('The "%s" class is deprecated since Symfony 4.4.', DoctrineCache::class), \E_USER_DEPRECATED);
final class DoctrineCache implements CacheInterface
{
 private $cache;
 public function __construct(Cache $cache)
 {
 $this->cache = $cache;
 }
 public function setCache(Cache $cache)
 {
 $this->cache = $cache;
 }
 public function has($class) : bool
 {
 return $this->cache->contains($class);
 }
 public function read($class)
 {
 return $this->cache->fetch($class);
 }
 public function write(ClassMetadata $metadata)
 {
 $this->cache->save($metadata->getClassName(), $metadata);
 }
}
