<?php
/*
Plugin Name: MemberPress Toolbox - Account Navigation Tabs
Plugin URI: https://meprtoolbox.com/product/account-navigation-tabs/
Description: Lets site owners easily add additional navigation tabs to the MemberPress Account page.
Version: 1.0.6
Author: MemberPress Toolbox
Author URI: https://meprtoolbox.com/
Text Domain: mant
Copyright: 2012-2020
*/

if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

if(is_plugin_active('memberpress/memberpress.php')) {
  // PATHS
  define("MANTPATH", dirname(__FILE__)); // Has NO trailing slash
  define("MANTVIEWSPATH", MANTPATH . '/app/views');
  // URLS
  define("MANTURL", plugin_dir_url(__FILE__)); // Has trailing slash
  define("MANTSCRIPTSURL", MANTURL . 'scripts');

  // Load up the controller(s)
  require_once(MANTPATH . '/app/controllers/AppCtrl.php');
  // Set globals
  global $mant_app_ctrl;
  // Populate globals
  $mant_app_ctrl = new MantAppCtrl(); // Hooks are in contsructor

  // Load up the helpers
  require_once(MANTPATH . '/app/helpers/AppHelper.php'); // MantAppHelper - static
}

/* CHANGE LOG
** 1.0.0
*** Initial Release
** 1.0.1 (7/22/2019)
*** Fix for BuddyPress integration
** 1.0.2, 1.0.3, 1.0.4
*** BuddyPress integration working much better, no meta refresh redirect needed now
** 1.0.5
*** Fix for non /account/ URL's
** 1.0.6
*** Support for Polylang/WPML admin texts
*/
