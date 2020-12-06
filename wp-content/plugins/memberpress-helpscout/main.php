<?php
/*
Plugin Name: MemberPress HelpScout
Plugin URI: http://www.memberpress.com/
Description: HelpScout integration for MemberPress.
Version: 1.0.7
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress-helpscout
Copyright: 2004-2016, Caseproof, LLC
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

include_once(ABSPATH . 'wp-admin/includes/plugin.php');
if(is_plugin_active('memberpress/memberpress.php')) {

  define('MPHELPSCOUT_PLUGIN_SLUG','memberpress-helpscout/main.php');
  define('MPHELPSCOUT_PLUGIN_NAME','memberpress-helpscout');
  define('MPHELPSCOUT_EDITION',MPHELPSCOUT_PLUGIN_NAME);
  define('MPHELPSCOUT_PATH',WP_PLUGIN_DIR.'/'.MPHELPSCOUT_PLUGIN_NAME);
  define('MPHELPSCOUT_VIEW_PATH',MPHELPSCOUT_PATH.'/views');
  $mphelpscout_url_protocol = (is_ssl())?'https':'http'; // Make all of our URLS protocol agnostic
  define('MPHELPSCOUT_URL',preg_replace('/^https?:/', "{$mphelpscout_url_protocol}:", plugins_url('/'.MPHELPSCOUT_PLUGIN_NAME)));

  // Load Addon
  require_once(MPHELPSCOUT_PATH . '/MpHelpScout.php');
  new MpHelpScout;

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(MPHELPSCOUT_PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPHELPSCOUT_EDITION,
    MPHELPSCOUT_PLUGIN_SLUG,
    'mphelpscout_license_key',
    __('MemberPress HelpScout', 'memberpress-helpscout'),
    __('HelpScout Custom App Integration for MemberPress.', 'memberpress-helpscout')
  );

}

