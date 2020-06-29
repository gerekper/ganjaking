<?php
/*
Plugin Name: MemberPress Mailster
Plugin URI: http://www.memberpress.com/
Description: Mailster Autoresponder integration for MemberPress.
Version: 1.1.0
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress-mailster
Copyright: 2004-2015, Caseproof, LLC
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if(is_plugin_active('memberpress/memberpress.php')) {
  define('MPMAILSTER_PLUGIN_SLUG','memberpress-mailster/main.php');
  define('MPMAILSTER_PLUGIN_NAME','memberpress-mailster');
  define('MPMAILSTER_EDITION',MPMAILSTER_PLUGIN_NAME);
  define('MPMAILSTER_PATH',WP_PLUGIN_DIR.'/'.MPMAILSTER_PLUGIN_NAME);
  $mpmailster_url_protocol = (is_ssl())?'https':'http'; // Make all of our URLS protocol agnostic
  define('MPMAILSTER_URL',preg_replace('/^https?:/', "{$mpmailster_url_protocol}:", plugins_url('/'.MPMAILSTER_PLUGIN_NAME)));

  // Load Addon
  require_once(MPMAILSTER_PATH . '/MpMailster.php');
  new MpMailster;

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(MPMAILSTER_PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPMAILSTER_EDITION,
    MPMAILSTER_PLUGIN_SLUG,
    'mpmailster_license_key',
    __('MemberPress Mailster', 'memberpress-mailster', 'memberpress-mailpoet'),
    __('Mailster Autoresponder Integration for MemberPress.', 'memberpress-mailster', 'memberpress-mailpoet')
  );
}
