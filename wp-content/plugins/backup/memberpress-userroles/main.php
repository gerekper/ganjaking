<?php
/*
Plugin Name: MemberPress User Roles
Plugin URI: http://www.memberpress.com/
Description: MemberPress Integration for WordPress User Roles.
Version: 1.0.5
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress-userroles
Copyright: 2004-2015, Caseproof, LLC
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if(is_plugin_active('memberpress/memberpress.php')) {
  define('MPUSERROLES_PLUGIN_SLUG','memberpress-userroles/main.php');
  define('MPUSERROLES_PLUGIN_NAME','memberpress-userroles');
  define('MPUSERROLES_EDITION',MPUSERROLES_PLUGIN_NAME);
  define('MPUSERROLES_PATH',WP_PLUGIN_DIR.'/'.MPUSERROLES_PLUGIN_NAME);
  $mpuserroles_url_protocol = (is_ssl())?'https':'http'; // Make all of our URLS protocol agnostic
  define('MPUSERROLES_URL',preg_replace('/^https?:/', "{$mpuserroles_url_protocol}:", plugins_url('/'.MPUSERROLES_PLUGIN_NAME)));

  // Load Addon
  require_once(MPUSERROLES_PATH . '/MpUserRoles.php');
  new MpUserRoles;

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(MPUSERROLES_PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPUSERROLES_EDITION,
    MPUSERROLES_PLUGIN_SLUG,
    'mpuserroles_license_key',
    __('MemberPress User Roles', 'memberpress-userroles'),
    __('User Roles add-on for MemberPress.', 'memberpress-userroles')
  );
}
