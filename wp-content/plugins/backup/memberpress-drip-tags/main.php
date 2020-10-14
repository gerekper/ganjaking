<?php
/*
Plugin Name: MemberPress Drip - Tags Version
Plugin URI: http://www.memberpress.com/
Description: Drip Autoresponder integration for MemberPress.
Version: 1.1.1
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress-drip-tags
Copyright: 2004-2015, Caseproof, LLC
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if(is_plugin_active('memberpress/memberpress.php')) {

  define('MPDRIPTAGS_PLUGIN_SLUG','memberpress-drip-tags/main.php');
  define('MPDRIPTAGS_PLUGIN_NAME','memberpress-drip-tags');
  define('MPDRIPTAGS_EDITION',MPDRIPTAGS_PLUGIN_NAME);
  define('MPDRIPTAGS_PATH',WP_PLUGIN_DIR.'/'.MPDRIPTAGS_PLUGIN_NAME);
  $mpdriptags_url_protocol = (is_ssl())?'https':'http'; // Make all of our URLS protocol agnostic
  define('MPDRIPTAGS_URL', preg_replace('/^https?:/', "{$mpdriptags_url_protocol}:", plugins_url('/'.MPDRIPTAGS_PLUGIN_NAME)));

  // Load Addon
  require_once(MPDRIPTAGS_PATH . '/MpDripTags.php');
  new MpDripTags;

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(MPDRIPTAGS_PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPDRIPTAGS_EDITION,
    MPDRIPTAGS_PLUGIN_SLUG,
    'mpdrip_license_key',
    __('MemberPress Drip - Tags', 'memberpress-drip-tags'),
    __('Drip Autoresponder Integration for MemberPress. Based on Tags.', 'memberpress-drip-tags')
  );
}
