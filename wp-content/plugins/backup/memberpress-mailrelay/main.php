<?php
/*
Plugin Name: MemberPress Mailrelay
Plugin URI: http://www.memberpress.com/
Description: Mailrelay Autoresponder integration for MemberPress.
Version: 1.0.5
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress-mailrelay
Copyright: 2004-2015, Caseproof, LLC
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

if(is_plugin_active('memberpress/memberpress.php')) {
  define('MPMAILRELAY_PLUGIN_SLUG','memberpress-mailrelay/main.php');
  define('MPMAILRELAY_PLUGIN_NAME','memberpress-mailrelay');
  define('MPMAILRELAY_EDITION',MPMAILRELAY_PLUGIN_NAME);
  define('MPMAILRELAY_PATH',WP_PLUGIN_DIR.'/'.MPMAILRELAY_PLUGIN_NAME);
  $mpmailrelay_url_protocol = (is_ssl())?'https':'http'; // Make all of our URLS protocol agnostic
  define('MPMAILRELAY_URL',preg_replace('/^https?:/', "{$mpmailrelay_url_protocol}:", plugins_url('/'.MPMAILRELAY_PLUGIN_NAME)));

  // Load Addon
  require_once(MPMAILRELAY_PATH . '/MpMailrelay.php');
  new MpMailrelay;

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(MPMAILRELAY_PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPMAILRELAY_EDITION,
    MPMAILRELAY_PLUGIN_SLUG,
    'mpmailrelay_license_key',
    __('MemberPress Mailrelay', 'memberpress-mailrelay'),
    __('Mailrelay Autoresponder Integration for MemberPress.', 'memberpress-mailrelay')
  );
}
