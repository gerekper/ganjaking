<?php
/*
Plugin Name: MemberPress Sendy
Plugin URI: http://www.memberpress.com/
Description: Sendy Autoresponder integration for MemberPress.
Version: 1.0.5
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress-sendy
Copyright: 2004-2015, Caseproof, LLC
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

if(is_plugin_active('memberpress/memberpress.php')) {
  define('MPSENDY_PLUGIN_SLUG','memberpress-sendy/main.php');
  define('MPSENDY_PLUGIN_NAME','memberpress-sendy');
  define('MPSENDY_EDITION',MPSENDY_PLUGIN_NAME);
  define('MPSENDY_PATH',WP_PLUGIN_DIR.'/'.MPSENDY_PLUGIN_NAME);
  $mpsendy_url_protocol = (is_ssl())?'https':'http'; // Make all of our URLS protocol agnostic
  define('MPSENDY_URL',preg_replace('/^https?:/', "{$mpsendy_url_protocol}:", plugins_url('/'.MPSENDY_PLUGIN_NAME)));

  // Load Addon
  require_once(MPSENDY_PATH . '/MpSendy.php');
  new MpSendy;

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(MPSENDY_PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPSENDY_EDITION,
    MPSENDY_PLUGIN_SLUG,
    'mpsendy_license_key',
    __('MemberPress Sendy', 'memberpress-sendy'),
    __('Sendy Autoresponder Integration for MemberPress.', 'memberpress-sendy')
  );
}
