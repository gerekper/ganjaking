<?php
namespace MailPoetVendor\Twig\Cache;
if (!defined('ABSPATH')) exit;
final class NullCache implements CacheInterface
{
 public function generateKey($name, $className)
 {
 return '';
 }
 public function write($key, $content)
 {
 }
 public function load($key)
 {
 }
 public function getTimestamp($key)
 {
 return 0;
 }
}
\class_alias('MailPoetVendor\\Twig\\Cache\\NullCache', 'MailPoetVendor\\Twig_Cache_Null');
