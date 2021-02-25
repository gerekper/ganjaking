<?php
/*
Plugin Name: MemberPress Toolbox - Manual Member Approval
Plugin URI: https://meprtoolbox.com/product/limit-signups/
Description: Manually approve members before they can access protected content.
Version: 1.1.6
Author: MemberPress Toolbox
Author URI: https://meprtoolbox.com/
Text Domain: mpmma
Copyright: 2012-2021
*/

if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

if(is_plugin_active('memberpress/memberpress.php')) {
  // PATHS
  define("MPMMAPATH", dirname(__FILE__)); // Has NO trailing slash
  define("MPMMAVIEWSPATH", MPMMAPATH . '/app/views');
  // URLS
  define("MPMMAURL", plugin_dir_url(__FILE__)); // Has trailing slash
  define("MPMMASCRIPTSURL", MPMMAURL . 'scripts');

  // Load up the controller(s)
  require_once(MPMMAPATH . '/app/controllers/AppCtrl.php');
  // Set globals
  global $mpmma_app_ctrl;
  // Populate globals
  $mpmma_app_ctrl = new MpmmaAppCtrl(); // Hooks are in contsructor
}

/** CHANGELOG
*** 1.1.6
***** Fix for Stripe on Single Page Checkout
*** 1.1.5
***** Fix small syntax typos
*** 1.1.4
***** Force text/html content type for emails
*** 1.1.3
***** Added option to login while rejected
*** 1.1.2
***** Added logging of who updated member's status last
*** 1.1.1
***** Added option to disable Held for Approval email
***** Don't let rejected members login
*** 1.1.0
***** Added option for MP template wrap on emails
***** Added option to allow users to stay logged in (hide content)
***** Added MemberPress email parameters support
***** Requires MP 1.8.5+
*** 1.0.0
***** Initial Release
**/
