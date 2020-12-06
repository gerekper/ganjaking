<?php
/*
Plugin Name: MemberPress Constant Contact
Plugin URI: http://www.memberpress.com/
Description: Constant Contact Autoresponder integration for MemberPress.
Version: 1.1.3
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress-constantcontact
Copyright: 2004-2015, Caseproof, LLC
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if(is_plugin_active('memberpress/memberpress.php')) {

  define('MPCONSTANTCONTACT_PLUGIN_SLUG','memberpress-constantcontact/main.php');
  define('MPCONSTANTCONTACT_PLUGIN_NAME','memberpress-constantcontact');
  define('MPCONSTANTCONTACT_EDITION',MPCONSTANTCONTACT_PLUGIN_NAME);
  define('MPCONSTANTCONTACT_PATH',WP_PLUGIN_DIR.'/'.MPCONSTANTCONTACT_PLUGIN_NAME);
  $mpconstantcontact_url_protocol = (is_ssl())?'https':'http'; // Make all of our URLS protocol agnostic
  define('MPCONSTANTCONTACT_URL',preg_replace('/^https?:/', "{$mpconstantcontact_url_protocol}:", plugins_url('/'.MPCONSTANTCONTACT_PLUGIN_NAME)));

  // Load Addon
  require_once(MPCONSTANTCONTACT_PATH . '/MpConstantContact.php');
  new MpConstantContact;

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(MPCONSTANTCONTACT_PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPCONSTANTCONTACT_EDITION,
    MPCONSTANTCONTACT_PLUGIN_SLUG,
    'mpconstantcontact_license_key',
    __('MemberPress Constant Contact', 'memberpress-constantcontact'),
    __('Constant Contact Autoresponder Integration for MemberPress.', 'memberpress-constantcontact')
  );

}

