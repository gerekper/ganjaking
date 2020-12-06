<?php
/*
Plugin Name: MemberPress MailChimp 3.0
Plugin URI: http://www.memberpress.com/
Description: MailChimp Autoresponder integration for MemberPress. Uses only 1 List and segments your members by Merge Tags.
Version: 1.2.2
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress-mailchimp-tags
Copyright: 2004-2015, Caseproof, LLC
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

include_once( ABSPATH . 'wp-admin/includes/plugin.php');
if(is_plugin_active('memberpress/memberpress.php')) {
  define('MPMAILCHIMPTAGS_PLUGIN_SLUG','memberpress-mailchimp-tags/main.php');
  define('MPMAILCHIMPTAGS_PLUGIN_NAME','memberpress-mailchimp-tags');
  define('MPMAILCHIMPTAGS_EDITION',MPMAILCHIMPTAGS_PLUGIN_NAME);
  define('MPMAILCHIMPTAGS_PATH',WP_PLUGIN_DIR.'/'.MPMAILCHIMPTAGS_PLUGIN_NAME);
  $mpmailchimp_url_protocol = (is_ssl())?'https':'http'; // Make all of our URLS protocol agnostic
  define('MPMAILCHIMPTAGS_URL', preg_replace('/^https?:/', "{$mpmailchimp_url_protocol}:", plugins_url('/'.MPMAILCHIMPTAGS_PLUGIN_NAME)));

  // Load Addon
  require_once(MPMAILCHIMPTAGS_PATH . '/MpMailChimpTags.php');
  new MpMailChimpTags;

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(MPMAILCHIMPTAGS_PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPMAILCHIMPTAGS_EDITION,
    MPMAILCHIMPTAGS_PLUGIN_SLUG,
    'mpmailchimptags_license_key',
    __('MemberPress MailChimp 3.0', 'memberpress-mailchimp-tags'),
    __('MailChimp Autoresponder Integration for MemberPress.', 'memberpress-mailchimp-tags')
  );
}
