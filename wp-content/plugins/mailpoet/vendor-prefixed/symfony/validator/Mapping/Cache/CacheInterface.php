<?php
namespace MailPoetVendor\Symfony\Component\Validator\Mapping\Cache;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Mapping\ClassMetadata;
@\trigger_error(\sprintf('The "%s" interface is deprecated since Symfony 4.4.', CacheInterface::class), \E_USER_DEPRECATED);
interface CacheInterface
{
 public function has($class);
 public function read($class);
 public function write(ClassMetadata $metadata);
}
