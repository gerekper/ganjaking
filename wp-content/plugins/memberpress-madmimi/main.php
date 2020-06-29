<?php
/*
Plugin Name: MemberPress Mad Mimi
Plugin URI: http://www.memberpress.com/
Description: Mad Mimi Autoresponder integration for MemberPress.
Version: 1.0.2
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress-madmimi
Copyright: 2004-2015, Caseproof, LLC
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

if(is_plugin_active('memberpress/memberpress.php')) {
  define('MPMADMIMI_PLUGIN_SLUG','memberpress-madmimi/main.php');
  define('MPMADMIMI_PLUGIN_NAME','memberpress-madmimi');
  define('MPMADMIMI_EDITION',MPMADMIMI_PLUGIN_NAME);
  define('MPMADMIMI_PATH',WP_PLUGIN_DIR.'/'.MPMADMIMI_PLUGIN_NAME);
  $mpmadmimi_url_protocol = (is_ssl())?'https':'http'; // Make all of our URLS protocol agnostic
  define('MPMADMIMI_URL',preg_replace('/^https?:/', "{$mpmadmimi_url_protocol}:", plugins_url('/'.MPMADMIMI_PLUGIN_NAME)));

  // Load Addon
  require_once(MPMADMIMI_PATH . '/MpMadMimi.php');
  new MpMadMimi;

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(MPMADMIMI_PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPMADMIMI_EDITION,
    MPMADMIMI_PLUGIN_SLUG,
    'mpmadmimi_license_key',
    __('MemberPress Mad Mimi', 'memberpress-madmimi'),
    __('Mad Mimi Autoresponder Integration for MemberPress.', 'memberpress-madmimi')
  );
}
