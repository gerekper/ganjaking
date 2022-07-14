<?php
namespace Composer\Platform;
if (!defined('ABSPATH')) exit;
class Runtime
{
 public function hasConstant($constant, $class = null)
 {
 return defined(ltrim($class.'::'.$constant, ':'));
 }
 public function getConstant($constant, $class = null)
 {
 return constant(ltrim($class.'::'.$constant, ':'));
 }
 public function hasFunction($fn)
 {
 return function_exists($fn);
 }
 public function invoke($callable, array $arguments = array())
 {
 return call_user_func_array($callable, $arguments);
 }
 public function hasClass($class)
 {
 return class_exists($class, false);
 }
 public function construct($class, array $arguments = array())
 {
 if (empty($arguments)) {
 return new $class;
 }
 $refl = new \ReflectionClass($class);
 return $refl->newInstanceArgs($arguments);
 }
 public function getExtensions()
 {
 return get_loaded_extensions();
 }
 public function getExtensionVersion($extension)
 {
 return phpversion($extension);
 }
 public function getExtensionInfo($extension)
 {
 $reflector = new \ReflectionExtension($extension);
 ob_start();
 $reflector->info();
 return ob_get_clean();
 }
}
