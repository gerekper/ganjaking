<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection\ParameterBag;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\LogicException;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
interface ParameterBagInterface
{
 public function clear();
 public function add(array $parameters);
 public function all();
 public function get($name);
 public function remove($name);
 public function set($name, $value);
 public function has($name);
 public function resolve();
 public function resolveValue($value);
 public function escapeValue($value);
 public function unescapeValue($value);
}
