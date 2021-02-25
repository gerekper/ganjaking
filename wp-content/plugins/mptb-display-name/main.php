<?php
/*
Plugin Name: MemberPress Toolbox - Set Display Name
Plugin URI: https://meprtoolbox.com/
Description: Updates User's display name in WordPress when they signup through MemberPress.
Version: 1.0.2
Author: MemberPress Toolbox
Author URI: https://meprtoolbox.com/
Text Domain: mpdn
Copyright: 2012-2020
*/

if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

if(is_plugin_active('memberpress/memberpress.php')) {
  // PATHS
  define("MPDNPATH", dirname(__FILE__)); // Has NO trailing slash
  define("MPDNVIEWSPATH", MPDNPATH . '/app/views');
  // URLS
  define("MPDNURL", plugin_dir_url(__FILE__)); // Has trailing slash
  define("MPDNSCRIPTSURL", MPDNURL . 'scripts');

  // Load up the controller(s)
  require_once(MPDNPATH . '/app/controllers/AppCtrl.php');
  // Set globals
  global $mpdn_app_ctrl;
  // Populate globals
  $mpdn_app_ctrl = new MpdnAppCtrl(); // Hooks are in contsructor
}

/* CHANGELOG
1.0.2
- Changed login update logic to use wp_login action
1.0.1
- Added option to enable/disable display name when saving WP User Profiles
- Don't update Display Name when logging in if Admin
1.0.0
- Initial Release
*/
