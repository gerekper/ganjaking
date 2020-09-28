<?php

namespace MailPoet\Premium\Config;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\Env as ParentEnv;
use MailPoet\WP\Functions as WPFunctions;

class Env {
  public static $version;
  public static $pluginName;
  public static $file;
  public static $path;
  public static $viewsPath;
  public static $assetsPath;
  public static $assetsUrl;
  public static $tempPath;
  public static $cachePath;
  public static $languagesPath;
  public static $libPath;
  public static $cdnAssetsBaseUrl;

  public static function init($file, $version) {
    self::$version = $version;
    self::$file = $file;
    self::$path = dirname(self::$file);
    self::$pluginName = 'mailpoet-premium';
    self::$viewsPath = self::$path . '/views';
    self::$assetsPath = self::$path . '/assets';
    self::$assetsUrl = WPFunctions::get()->pluginsUrl('/assets', $file);
    // Use MailPoet Free's upload dir to prevent it from being altered
    // due to late Premium initialization, just replace the plugin name at the end
    self::$tempPath = preg_replace('/' . ParentEnv::$pluginName . '$/', self::$pluginName, ParentEnv::$tempPath);
    if (is_string(self::$tempPath)) {
      self::$cachePath = self::$tempPath . '/cache';
    } else {
      throw new \Exception('Cache folder is invalid');
    }
    self::$languagesPath = self::$path . '/lang';
    self::$libPath = self::$path . '/lib';
    self::$cdnAssetsBaseUrl = ParentEnv::$baseUrl;
  }
}
