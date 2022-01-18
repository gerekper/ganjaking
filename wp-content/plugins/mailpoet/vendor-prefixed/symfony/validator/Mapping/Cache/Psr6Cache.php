<?php
namespace MailPoetVendor\Symfony\Component\Validator\Mapping\Cache;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Psr\Cache\CacheItemPoolInterface;
use MailPoetVendor\Symfony\Component\Validator\Mapping\ClassMetadata;
@\trigger_error(\sprintf('The "%s" class is deprecated since Symfony 4.4.', Psr6Cache::class), \E_USER_DEPRECATED);
class Psr6Cache implements CacheInterface
{
 private $cacheItemPool;
 public function __construct(CacheItemPoolInterface $cacheItemPool)
 {
 $this->cacheItemPool = $cacheItemPool;
 }
 public function has($class)
 {
 return $this->cacheItemPool->hasItem($this->escapeClassName($class));
 }
 public function read($class)
 {
 $item = $this->cacheItemPool->getItem($this->escapeClassName($class));
 if (!$item->isHit()) {
 return \false;
 }
 return $item->get();
 }
 public function write(ClassMetadata $metadata)
 {
 $item = $this->cacheItemPool->getItem($this->escapeClassName($metadata->getClassName()));
 $item->set($metadata);
 $this->cacheItemPool->save($item);
 }
 private function escapeClassName(string $class) : string
 {
 if (\str_contains($class, '@')) {
 // anonymous class: replace all PSR6-reserved characters
 return \str_replace(["\0", '\\', '/', '@', ':', '{', '}', '(', ')'], '.', $class);
 }
 return \str_replace('\\', '.', $class);
 }
}
