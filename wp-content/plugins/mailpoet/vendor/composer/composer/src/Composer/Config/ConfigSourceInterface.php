<?php
namespace Composer\Config;
if (!defined('ABSPATH')) exit;
interface ConfigSourceInterface
{
 public function addRepository($name, $config, $append = true);
 public function removeRepository($name);
 public function addConfigSetting($name, $value);
 public function removeConfigSetting($name);
 public function addProperty($name, $value);
 public function removeProperty($name);
 public function addLink($type, $name, $value);
 public function removeLink($type, $name);
 public function getName();
}
