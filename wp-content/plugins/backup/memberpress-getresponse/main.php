<?php
/*
Plugin Name: MemberPress GetResponse
Plugin URI: http://www.memberpress.com/
Description: GetResponse Autoresponder integration for MemberPress.
Version: 1.1.0
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress-getresponse
Copyright: 2004-2015, Caseproof, LLC
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

include_once(ABSPATH . 'wp-admin/includes/plugin.php');
if(is_plugin_active('memberpress/memberpress.php')) {

  define('MPGETRESPONSE_PLUGIN_SLUG','memberpress-getresponse/main.php');
  define('MPGETRESPONSE_PLUGIN_NAME','memberpress-getresponse');
  define('MPGETRESPONSE_EDITION',MPGETRESPONSE_PLUGIN_NAME);
  define('MPGETRESPONSE_PATH',WP_PLUGIN_DIR.'/'.MPGETRESPONSE_PLUGIN_NAME);
  $mpgetresponse_url_protocol = (is_ssl())?'https':'http'; // Make all of our URLS protocol agnostic
  define('MPGETRESPONSE_URL',preg_replace('/^https?:/', "{$mpgetresponse_url_protocol}:", plugins_url('/'.MPGETRESPONSE_PLUGIN_NAME)));

  // Load Addon
  require_once(MPGETRESPONSE_PATH . '/MpGetResponse.php');
  new MpGetResponse;

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(MPGETRESPONSE_PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPGETRESPONSE_EDITION,
    MPGETRESPONSE_PLUGIN_SLUG,
    'mpgetresponse_license_key',
    __('MemberPress GetResponse', 'memberpress-getresponse'),
    __('GetResponse Autoresponder Integration for MemberPress.', 'memberpress-getresponse')
  );

}

