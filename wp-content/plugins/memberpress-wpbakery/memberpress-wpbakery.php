<?php
/*
Plugin Name: MemberPress WPBakery Content Protection
Plugin URI: http://www.memberpress.com/
Description: WPBakery (Visual Composer) integration to protect content with MemberPress.
Version: 1.0.0
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress-wpbakery
Copyright: 2004-2015, Caseproof, LLC
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if(is_plugin_active('memberpress/memberpress.php')) {

  define('MPWPBAKERY_PLUGIN_SLUG','memberpress-wpbakery/memberpress-wpbakery.php');
  define('MPWPBAKERY_PLUGIN_NAME','memberpress-wpbakery');
  define('MPWPBAKERY_EDITION',MPWPBAKERY_PLUGIN_NAME);
  define('MPWPBAKERY_PATH',WP_PLUGIN_DIR.'/'.MPWPBAKERY_PLUGIN_NAME);

  // Load Addon
  require_once(MPWPBAKERY_PATH . '/MpWpBakery.php');
  new MpWpBakery;

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(MPWPBAKERY_PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPWPBAKERY_EDITION,
    MPWPBAKERY_PLUGIN_SLUG,
    'mpwpbakery_license_key',
    __('MemberPress WPBakery', 'memberpress-wpbakery'),
    __('WPBakery (Visual Composer) Builder integration to protect content with MemberPress.', 'memberpress-wpbakery')
  );

}
