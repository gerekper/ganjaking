<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Premium\Config;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\Env as ParentEnv;
use MailPoet\WP\Functions as WPFunctions;

class Env {
  public static $version;
  public static $pluginName;
  public static $pluginPath;
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
    self::$pluginPath = 'mailpoet-premium/mailpoet-premium.php';
    self::$viewsPath = self::$path . '/views';
    self::$assetsPath = self::$path . '/assets';

    self::$assetsUrl = defined('ABSPATH') ? WPFunctions::get()->pluginsUrl('/assets', $file) : '';
    // Use MailPoet Free's upload dir to prevent it from being altered
    // due to late Premium initialization, just replace the plugin name at the end
    self::$tempPath = preg_replace('/' . ParentEnv::$pluginName . '$/', self::$pluginName, ParentEnv::$tempPath);
    self::$cachePath = self::$path . '/generated/twig/';
    self::$languagesPath = ParentEnv::$languagesPath;
    self::$libPath = self::$path . '/lib';
    self::$cdnAssetsBaseUrl = ParentEnv::$baseUrl;
  }
}
