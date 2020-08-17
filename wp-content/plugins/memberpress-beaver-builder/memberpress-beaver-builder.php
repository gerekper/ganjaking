<?php
/*
Plugin Name: MemberPress Beaver Builder Content Protection
Plugin URI: http://www.memberpress.com/
Description: Beaver Builder integration to protect content with MemberPress.
Version: 1.0.4
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress-beaver-builder
Copyright: 2004-2015, Caseproof, LLC
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if(is_plugin_active('memberpress/memberpress.php')) {

  define('MPBEAVERBUILDER_PLUGIN_SLUG','memberpress-beaver-builder/memberpress-beaver-builder.php');
  define('MPBEAVERBUILDER_PLUGIN_NAME','memberpress-beaver-builder');
  define('MPBEAVERBUILDER_EDITION',MPBEAVERBUILDER_PLUGIN_NAME);
  define('MPBEAVERBUILDER_PATH',WP_PLUGIN_DIR.'/'.MPBEAVERBUILDER_PLUGIN_NAME);

  // Load Addon
  require_once(MPBEAVERBUILDER_PATH . '/MpBeaverBuilder.php');
  new MpBeaverBuilder;

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(MPBEAVERBUILDER_PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPBEAVERBUILDER_EDITION,
    MPBEAVERBUILDER_PLUGIN_SLUG,
    'mpbeaverbuilder_license_key',
    __('MemberPress Beaver', 'memberpress-beaver-builder'),
    __('Beaver Builder integration to protect content with MemberPress.', 'memberpress-beaver-builder')
  );

}
