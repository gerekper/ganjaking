<?php
/*
Plugin Name: Affiliate Royale MemberPress Edition
Plugin URI: http://www.affiliateroyale.com
Description: A complete Affiliate Program plugin for WordPress. Use it to start an Affiliate Program for your products to dramatically increase traffic, attention and sales.
Version: 1.4.15
Author: Caseproof, LLC
Text Domain: affiliate-royale
Copyright: 2004-2015, Caseproof, LLC

GNU General Public License, Free Software Foundation <http://creativecommons.org/licenses/GPL/2.0/>
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
define('WAFP_PLUGIN_SLUG',plugin_basename(__FILE__));
define('WAFP_PLUGIN_NAME',dirname(plugin_basename(__FILE__)));
$wafp_script_url = get_site_url() . '/index.php?plugin=wafp';
define('WAFP_PATH',WP_PLUGIN_DIR.'/'.WAFP_PLUGIN_NAME);
define('WAFP_IMAGES_PATH',WAFP_PATH.'/images');
define('WAFP_CSS_PATH',WAFP_PATH.'/css');
define('WAFP_JS_PATH',WAFP_PATH.'/js');
define('WAFP_I18N_PATH',WAFP_PATH.'/i18n');
define('WAFP_MODELS_PATH',WAFP_PATH.'/app/models');
define('WAFP_CONTROLLERS_PATH',WAFP_PATH.'/app/controllers');
define('WAFP_LIB_PATH',WAFP_PATH.'/app/lib');
define('WAFP_VIEWS_PATH',WAFP_PATH.'/app/views');
define('WAFP_HELPERS_PATH',WAFP_PATH.'/app/helpers');
define('WAFP_TESTS_PATH',WAFP_PATH.'/tests');
define('WAFP_VENDOR_PATH',WAFP_PATH.'/vendor');
define('WAFP_URL',plugins_url($path = '/'.WAFP_PLUGIN_NAME));
define('WAFP_IMAGES_URL',WAFP_URL.'/images');
define('WAFP_CSS_URL',WAFP_URL.'/css');
define('WAFP_JS_URL',WAFP_URL.'/js');
define('WAFP_SCRIPT_URL',$wafp_script_url);
define('WAFP_VENDOR_URL',WAFP_URL.'/vendor');
define('WAFP_EDITION','affiliate-royale-mp');

/**
 * Returns current plugin version.
 *
 * @return string Plugin version
 */
function wafp_plugin_info($field) {
  static $plugin_folder, $plugin_file;

  if( !isset($plugin_folder) or !isset($plugin_file) ) {
    if( ! function_exists( 'get_plugins' ) )
      require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

    $plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
    $plugin_file = basename( ( __FILE__ ) );
  }

  if(isset($plugin_folder[$plugin_file][$field]))
    return $plugin_folder[$plugin_file][$field];

  return '';
}

// Plugin Information from the plugin header declaration
define('WAFP_VERSION', wafp_plugin_info('Version'));
define('WAFP_DISPLAY_NAME', wafp_plugin_info('Name'));
define('WAFP_AUTHOR', wafp_plugin_info('Author'));
define('WAFP_AUTHOR_URI', wafp_plugin_info('AuthorURI'));
define('WAFP_DESCRIPTION', wafp_plugin_info('Description'));

// Autoload all the requisite classes
function wafp_autoloader($class_name) {
  // Only load Affiliate Royale classes here
  if(preg_match('/^Wafp.+$/', $class_name))
  {
    if(preg_match('/^.+Controller$/', $class_name))
      $filepath = WAFP_CONTROLLERS_PATH . "/{$class_name}.php";
    else if(preg_match('/^.+Helper$/', $class_name))
      $filepath = WAFP_HELPERS_PATH . "/{$class_name}.php";
    else {
      $filepath = WAFP_MODELS_PATH . "/{$class_name}.php";

      // Now let's try the lib dir if its not a model
      if(!file_exists($filepath))
        $filepath = WAFP_LIB_PATH . "/{$class_name}.php";
    }

    if(file_exists($filepath))
      require_once($filepath);
  }
}

// if __autoload is active, put it on the spl_autoload stack
if( is_array(spl_autoload_functions()) and
    in_array('__autoload', spl_autoload_functions()) ) {
   spl_autoload_register('__autoload');
}

// Add the autoloader
spl_autoload_register('wafp_autoloader');

// Gotta load the language before everything else
WafpAppController::load_language();

// queue the update shizzle
WafpUpdateController::load_hooks();

// More Global variables
global $wafp_blogurl;
global $wafp_siteurl;
global $wafp_blogname;
global $wafp_blogdescription;

$wafp_blogurl         = ((get_option('home'))?get_option('home'):get_option('siteurl'));
$wafp_siteurl         = get_option('siteurl');
$wafp_blogname        = get_option('blogname');
$wafp_blogdescription = get_option('blogdescription');

define('WAFP_BLOGURL', $wafp_blogurl);
define('WAFP_SITEURL', $wafp_siteurl);
define('WAFP_BLOGNAME', $wafp_blogname);
define('WAFP_BLOGDESCRIPTION', $wafp_blogdescription);

global $wafp_db_version;
$wafp_db_version = 19; // this is the version of the database we're moving to

/***** SETUP OPTIONS OBJECT *****/
global $wafp_db;
global $wafp_options;
$wafp_db      = new WafpDb(); //needs to come before options
$wafp_options = WafpOptions::fetch();

// Instansiate Models
global $wafp_utils;
$wafp_utils  = new WafpUtils();

WafpSubscription::register();

// Instansiate Controllers
WafpAppController::load_hooks();
WafpDashboardController::load_hooks();
WafpOptionsController::load_hooks();
WafpShortcodesController::load_hooks();
WafpAweberController::load_hooks();
WafpMailChimpController::load_hooks();
WafpGetResponseController::load_hooks();
WafpConvertKitController::load_hooks();
WafpUsersController::load_hooks();

// Setup integrations
if( in_array('paypal', $wafp_options->integration) or in_array('wishlist', $wafp_options->integration) )
  WafpPayPalController::load_hooks();

if( in_array('authorize', $wafp_options->integration) )
  WafpAuthorizeController::load_hooks();

if( in_array('easy_digital_downloads', $wafp_options->integration) )
  WafpEasyDigitalDownloadsController::load_hooks();

if( in_array('jigoshop', $wafp_options->integration) )
  WafpJigoshopController::load_hooks();

if( in_array('marketpress', $wafp_options->integration) )
  WafpMarketPressController::load_hooks();

if( in_array('memberpress', $wafp_options->integration) )
  WafpMemberPressController::load_hooks();

if( in_array('shopp', $wafp_options->integration) )
  WafpShoppController::load_hooks();

if( in_array('woocommerce', $wafp_options->integration) )
  WafpWooCommerceController::load_hooks();

if( in_array('ecommerce', $wafp_options->integration) )
  WafpECommerceController::load_hooks();

if( in_array('cart66', $wafp_options->integration) )
  WafpCart66Controller::load_hooks();

if( in_array('diglabs_stripe_payments', $wafp_options->integration) )
  WafpDigLabsStripePaymentsController::load_hooks();

if( in_array('super_stripe', $wafp_options->integration) )
  WafpSuperStripeController::load_hooks();

// Instansiate Helpers

// Setup screens
WafpAppController::setup_menus();

// Include Widgets

// Register Widgets

// Include APIs

// Template Tags
