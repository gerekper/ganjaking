<?php
/*
Plugin Name: MemberPress Delightful Downloads
Plugin URI: http://www.memberpress.com/
Description: Seamless integration of Delightful Downloads and MemberPress
Version: 1.0.2
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress-delightfuldownloads
Copyright: 2004-2015, Caseproof, LLC
*/

if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

include_once(ABSPATH . 'wp-admin/includes/plugin.php');
if(is_plugin_active('memberpress/memberpress.php')) {
  define('MPDELDL_PLUGIN_SLUG', 'memberpress-delightfuldownloads/main.php');
  define('MPDELDL_PLUGIN_NAME', 'memberpress-delightfuldownloads');
  define('MPDELDL_EDITION', MPDELDL_PLUGIN_NAME);
  define('MPDELDL_PATH', WP_PLUGIN_DIR.'/'.MPDELDL_PLUGIN_NAME);
  $mpdeldl_url_protocol = (is_ssl())?'https':'http'; // Make all of our URLS protocol agnostic
  define('MPDELDL_URL', preg_replace('/^https?:/', "{$mpdeldl_url_protocol}:", plugins_url('/'.MPDELDL_PLUGIN_NAME)));

  // Load Addon
  require_once(MPDELDL_PATH . '/MpDelightfulDownloads.php');
  new MpDelightfulDownloads;

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(MPDELDL_PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPDELDL_EDITION,
    MPDELDL_PLUGIN_SLUG,
    'mpdeldl_license_key',
    __('MemberPress Delightful Downloads', 'memberpress-delightfuldownloads'),
    __('Delightful Downloads Integration for MemberPress.', 'memberpress-delightfuldownloads')
  );
}
