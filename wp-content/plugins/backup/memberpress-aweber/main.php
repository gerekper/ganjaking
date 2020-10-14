<?php
/*
Plugin Name: MemberPress AWeber
Plugin URI: http://www.memberpress.com/
Description: AWeber Autoresponder integration for MemberPress.
Version: 1.1.0
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress-aweber
Copyright: 2004-2015, Caseproof, LLC
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if(is_plugin_active('memberpress/memberpress.php')) {

  define('MPAWEBER_PLUGIN_SLUG','memberpress-aweber/main.php');
  define('MPAWEBER_PLUGIN_NAME','memberpress-aweber');
  define('MPAWEBER_EDITION',MPAWEBER_PLUGIN_NAME);
  define('MPAWEBER_PATH',WP_PLUGIN_DIR.'/'.MPAWEBER_PLUGIN_NAME);
  $mpaweber_url_protocol = (is_ssl())?'https':'http'; // Make all of our URLS protocol agnostic
  define('MPAWEBER_URL',preg_replace('/^https?:/', "{$mpaweber_url_protocol}:", plugins_url('/'.MPAWEBER_PLUGIN_NAME)));

  // Load Addon
  require_once(MPAWEBER_PATH . '/MpAWeber.php');
  new MpAWeber;

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(MPAWEBER_PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPAWEBER_EDITION,
    MPAWEBER_PLUGIN_SLUG,
    'mpaweber_license_key',
    __('MemberPress AWeber', 'memberpress-aweber'),
    __('AWeber Autoresponder Integration for MemberPress.', 'memberpress-aweber')
  );

}

