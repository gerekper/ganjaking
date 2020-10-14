<?php
/*
Plugin Name: MemberPress Elementor Content Protection
Plugin URI: http://www.memberpress.com/
Description: Elementor integration to protect content with MemberPress.
Version: 1.0.1
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress-elementor
Copyright: 2004-2015, Caseproof, LLC
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if(is_plugin_active('memberpress/memberpress.php')) {

  define('MPELEMENTOR_PLUGIN_SLUG','memberpress-elementor/memberpress-elementor.php');
  define('MPELEMENTOR_PLUGIN_NAME','memberpress-elementor');
  define('MPELEMENTOR_EDITION',MPELEMENTOR_PLUGIN_NAME);
  define('MPELEMENTOR_PATH',WP_PLUGIN_DIR.'/'.MPELEMENTOR_PLUGIN_NAME);

  // Load Addon
  require_once(MPELEMENTOR_PATH . '/MpElementor.php');
  new MpElementor;

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(MPELEMENTOR_PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPELEMENTOR_EDITION,
    MPELEMENTOR_PLUGIN_SLUG,
    'mpelementor_license_key',
    __('MemberPress Elementor', 'memberpress-elementor'),
    __('Elementor integration to protect content with MemberPress.', 'memberpress-elementor')
  );

}
