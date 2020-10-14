<?php
/*
Plugin Name: MemberPress + BuddyPress Integration
Plugin URI: http://www.memberpress.com/
Description: Integrates MemberPress with BuddyPress
Version: 1.1.7
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress-buddypress
Copyright: 2004-2015, Caseproof, LLC
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if(is_plugin_active('memberpress/memberpress.php')) {
  define('MPBP_PLUGIN_SLUG','memberpress-buddypress/main.php');
  define('MPBP_PLUGIN_NAME','memberpress-buddypress');
  define('MPBP_EDITION',MPBP_PLUGIN_NAME);
  define('MPBP_PATH',WP_PLUGIN_DIR.'/'.MPBP_PLUGIN_NAME);
  define('MPBP_I18N_PATH',MPBP_PATH.'/i18n');
  $mpbp_url_protocol = (is_ssl())?'https':'http'; // Make all of our URLS protocol agnostic
  define('MPBP_URL', preg_replace('/^https?:/', "{$mpbp_url_protocol}:", plugins_url('/'.MPBP_PLUGIN_NAME)));

  // Load Addon
  require_once(MPBP_PATH . '/MpBuddyPress.php');
  new MpBuddyPress;

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(MPBP_PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPBP_EDITION,
    MPBP_PLUGIN_SLUG,
    'mpbp_license_key',
    __('MemberPress + BuddyPress', 'memberpress-buddypress'),
    __('BuddyPress Integration for MemberPress.', 'memberpress-buddypress')
  );
}

function mepr_buddypress_load_language() {
  $path = str_replace(WP_PLUGIN_DIR, '', MPBP_I18N_PATH);
  load_plugin_textdomain('memberpress-buddypress', false, $path);
}
add_action('plugins_loaded', 'mepr_buddypress_load_language');
