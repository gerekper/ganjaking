<?php
/*
Plugin Name: MemberPress Courses
Plugin URI: https://memberpress.com/
Description: Create Courses that work seamlessly with MemberPress.
Version: 1.0.21
Requires at least: 5.0
Author: Caseproof LLC
Author URI: https://caseproof.com/
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: memberpress-courses
Domain Path: /i18n
Copyright: 2004-2021, Caseproof, LLC
*/

namespace memberpress\courses;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

/**
 * Returns current plugin version.
 *
 * @return string Plugin version
 */
function plugin_info($field) {
  static $plugin_folder, $plugin_file;

  if( !isset($plugin_folder) or !isset($plugin_file) ) {
    if( ! function_exists( 'get_plugins' ) ) {
      require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
    }

    $plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
    $plugin_file = basename( ( __FILE__ ) );
  }

  if(isset($plugin_folder[$plugin_file][$field])) {
    return $plugin_folder[$plugin_file][$field];
  }

  return '';
}

// Plugin Information from the plugin header declaration
define(__NAMESPACE__ . '\ROOT_NAMESPACE', __NAMESPACE__);
define(ROOT_NAMESPACE . '\VERSION', plugin_info('Version'));
define(ROOT_NAMESPACE . '\DISPLAY_NAME', plugin_info('Name'));
define(ROOT_NAMESPACE . '\AUTHOR', plugin_info('Author'));
define(ROOT_NAMESPACE . '\AUTHOR_URI', plugin_info('AuthorURI'));
define(ROOT_NAMESPACE . '\DESCRIPTION', plugin_info('Description'));

use \memberpress\courses\lib as lib;
use \memberpress\courses\controllers as ctrl;

// Requires MemberPress
if((defined('TESTS_RUNNING') && TESTS_RUNNING) || is_plugin_active('memberpress/memberpress.php')) {
  // Set all path / url variables
  $mpcs_url_protocol = (is_ssl())?'https':'http'; // Make all of our URLS protocol agnostic

  define(ROOT_NAMESPACE . '\CTRLS_NAMESPACE', ROOT_NAMESPACE . '\controllers');
  define(ROOT_NAMESPACE . '\ADMIN_CTRLS_NAMESPACE', ROOT_NAMESPACE . '\controllers\admin');
  define(ROOT_NAMESPACE . '\HELPERS_NAMESPACE', ROOT_NAMESPACE . '\helpers');
  define(ROOT_NAMESPACE . '\MODELS_NAMESPACE', ROOT_NAMESPACE . '\models');
  define(ROOT_NAMESPACE . '\LIB_NAMESPACE', ROOT_NAMESPACE . '\lib');
  define(ROOT_NAMESPACE . '\PLUGIN_SLUG', 'memberpress-courses/main.php');
  define(ROOT_NAMESPACE . '\PLUGIN_NAME', 'memberpress-courses');
  define(ROOT_NAMESPACE . '\SLUG_KEY', 'mpcs');
  define(ROOT_NAMESPACE . '\EDITION', PLUGIN_NAME);
  define(ROOT_NAMESPACE . '\PATH', WP_PLUGIN_DIR . '/' . PLUGIN_NAME);
  define(ROOT_NAMESPACE . '\CTRLS_PATH', PATH . '/app/controllers');
  define(ROOT_NAMESPACE . '\ADMIN_CTRLS_PATH', PATH . '/app/controllers/admin');
  define(ROOT_NAMESPACE . '\HELPERS_PATH', PATH . '/app/helpers');
  define(ROOT_NAMESPACE . '\MODELS_PATH', PATH . '/app/models');
  define(ROOT_NAMESPACE . '\LIB_PATH', PATH . '/app/lib');
  define(ROOT_NAMESPACE . '\CONFIG_PATH', PATH . '/app/config');
  define(ROOT_NAMESPACE . '\VIEWS_PATH', PATH . '/app/views');
  define(ROOT_NAMESPACE . '\IMAGES_PATH', PATH . '/public/images');
  define(ROOT_NAMESPACE . '\JS_PATH', PATH . '/public/js');
  define(ROOT_NAMESPACE . '\URL', preg_replace('/^https?:/', "{$mpcs_url_protocol}:", plugins_url('/' . PLUGIN_NAME)));
  define(ROOT_NAMESPACE . '\JS_URL', URL . '/public/js');
  define(ROOT_NAMESPACE . '\CSS_URL', URL . '/public/css');
  define(ROOT_NAMESPACE . '\IMAGES_URL', URL . '/public/images');
  define(ROOT_NAMESPACE . '\FONTS_URL', URL . '/public/fonts');
  define(ROOT_NAMESPACE . '\DB_VERSION', 4);

  // Autoload all the requisite classes
  function autoloader($class_name) {
    // Only load MemberPress Courses Classes
    if(0 === strpos($class_name, ROOT_NAMESPACE)) {
      preg_match('/([^\\\]*)$/', $class_name, $m);

      $file_name = $m[1];
      $filepath = '';

      if(preg_match('/' . preg_quote(LIB_NAMESPACE) . '\\\.*Exception/', $class_name)) {
        $filepath = LIB_PATH."/Exception.php";
      }
      else if(0 === strpos($class_name, LIB_NAMESPACE . '\Validatable/')) {
        $filepath = LIB_PATH."/{$file_name}.php";
      }
      else if(0 === strpos($class_name, LIB_NAMESPACE . '\Base/')) {
        $filepath = LIB_PATH."/{$file_name}.php";
      }
      else if(0 === strpos($class_name, ADMIN_CTRLS_NAMESPACE)) {
        $filepath = ADMIN_CTRLS_PATH."/{$file_name}.php";
      }
      else if(0 === strpos($class_name, CTRLS_NAMESPACE)) {
        $filepath = CTRLS_PATH."/{$file_name}.php";
      }
      else if(0 === strpos($class_name, HELPERS_NAMESPACE)) {
        $filepath = HELPERS_PATH."/{$file_name}.php";
      }
      else if(0 === strpos($class_name, MODELS_NAMESPACE)) {
        $filepath = MODELS_PATH."/{$file_name}.php";
      }
      else if(0 === strpos($class_name, LIB_NAMESPACE)) {
        $filepath = LIB_PATH."/{$file_name}.php";
      }

      if(file_exists($filepath)) {
        require_once($filepath);
      }
    }
  }

  // if __autoload is active, put it on the spl_autoload stack
  if( is_array(spl_autoload_functions()) &&
      in_array('__autoload', spl_autoload_functions()) ) {
     spl_autoload_register('__autoload');
  }

  // Add the autoloader
  spl_autoload_register(ROOT_NAMESPACE . '\autoloader');

  // Instansiate Ctrls
  lib\CtrlFactory::all();

  // Setup screens
  ctrl\App::setup_menus();

  register_activation_hook(PLUGIN_SLUG, function() { require_once(LIB_PATH . "/activation.php"); });
  register_deactivation_hook(PLUGIN_SLUG, function() { require_once(LIB_PATH . "/deactivation.php"); });

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new \MeprAddonUpdates(
    EDITION,
    PLUGIN_SLUG,
    'mpcourses_license_key',
    __('MemberPress Courses', 'memberpress-courses'),
    __('Courses add-on for MemberPress.', 'memberpress-courses')
  );
}
