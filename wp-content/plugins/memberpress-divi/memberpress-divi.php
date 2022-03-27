<?php
/*
Plugin Name: MemberPress Divi Content Protection
Plugin URI: http://www.memberpress.com/
Description: Divi Builder integration to protect content with MemberPress.
Version: 1.0.8
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress-divi
Copyright: 2004-2015, Caseproof, LLC
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if(is_plugin_active('memberpress/memberpress.php')) {

  define('MPDIVI_PLUGIN_SLUG','memberpress-divi/memberpress-divi.php');
  define('MPDIVI_PLUGIN_NAME','memberpress-divi');
  define('MPDIVI_EDITION',MPDIVI_PLUGIN_NAME);
  define('MPDIVI_PATH',WP_PLUGIN_DIR.'/'.MPDIVI_PLUGIN_NAME);

  // Load Addon
  require_once(MPDIVI_PATH . '/MpDivi.php');
  new MpDivi;

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(MPDIVI_PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPDIVI_EDITION,
    MPDIVI_PLUGIN_SLUG,
    'mpdivi_license_key',
    __('MemberPress Divi', 'memberpress-divi'),
    __('Divi Builder integration to protect content with MemberPress.', 'memberpress-divi')
  );

}
