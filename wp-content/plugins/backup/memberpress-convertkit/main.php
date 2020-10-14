<?php
/*
Plugin Name: MemberPress ConvertKit
Plugin URI: http://www.memberpress.com/
Description: ConvertKit Autoresponder integration for MemberPress.
Version: 1.2.1
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress-convertkit
Copyright: 2004-2015, Caseproof, LLC
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

if(is_plugin_active('memberpress/memberpress.php')) {
  define('MPCONVERTKIT_PLUGIN_SLUG','memberpress-convertkit/main.php');
  define('MPCONVERTKIT_PLUGIN_NAME','memberpress-convertkit');
  define('MPCONVERTKIT_EDITION',MPCONVERTKIT_PLUGIN_NAME);
  define('MPCONVERTKIT_PATH',WP_PLUGIN_DIR.'/'.MPCONVERTKIT_PLUGIN_NAME);
  $mpconvertkit_url_protocol = (is_ssl())?'https':'http'; // Make all of our URLS protocol agnostic
  define('MPCONVERTKIT_URL',preg_replace('/^https?:/', "{$mpconvertkit_url_protocol}:", plugins_url('/'.MPCONVERTKIT_PLUGIN_NAME)));

  // Load Addon
  require_once(MPCONVERTKIT_PATH . '/MpConvertKit.php');
  new MpConvertKit;

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(MPCONVERTKIT_PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPCONVERTKIT_EDITION,
    MPCONVERTKIT_PLUGIN_SLUG,
    'mpconvertkit_license_key',
    __('MemberPress ConvertKit', 'memberpress-convertkit'),
    __('ConvertKit Autoresponder Integration for MemberPress.', 'memberpress-convertkit')
  );
}
