<?php
/*
Plugin Name: MemberPress MailPoet
Plugin URI: http://www.memberpress.com/
Description: MailPoet Autoresponder integration for MemberPress.
Version: 1.2.0
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress-mailpoet
Copyright: 2004-2015, Caseproof, LLC
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if(is_plugin_active('memberpress/memberpress.php')) {
  define('MPMAILPOET_PLUGIN_SLUG','memberpress-mailpoet/main.php');
  define('MPMAILPOET_PLUGIN_NAME','memberpress-mailpoet');
  define('MPMAILPOET_EDITION',MPMAILPOET_PLUGIN_NAME);
  define('MPMAILPOET_PATH',WP_PLUGIN_DIR.'/'.MPMAILPOET_PLUGIN_NAME);
  $mpmailpoet_url_protocol = (is_ssl())?'https':'http'; // Make all of our URLS protocol agnostic
  define('MPMAILPOET_URL',preg_replace('/^https?:/', "{$mpmailpoet_url_protocol}:", plugins_url('/'.MPMAILPOET_PLUGIN_NAME)));

  // Load Addon
  require_once(MPMAILPOET_PATH . '/MpMailPoet.php');
  new MpMailPoet;

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(MPMAILPOET_PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPMAILPOET_EDITION,
    MPMAILPOET_PLUGIN_SLUG,
    'mpmailpoet_license_key',
    __('MemberPress MailPoet', 'memberpress-mailpoet'),
    __('MailPoet Autoresponder Integration for MemberPress.', 'memberpress-mailpoet')
  );
}
