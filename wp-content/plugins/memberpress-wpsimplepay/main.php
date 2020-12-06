<?php
/*
Plugin Name: MemberPress WP Simple Pay Pro
Plugin URI: http://www.memberpress.com/
Description: WP Simple Pay Pro integration for MemberPress.
Version: 1.0.3
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress-wpsimplepay
Copyright: 2004-2018, Caseproof, LLC
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

if(is_plugin_active('memberpress/memberpress.php')) {
  define('MPWPSP_PLUGIN_SLUG','memberpress-wpsimplepay/main.php');
  define('MPWPSP_PLUGIN_NAME','memberpress-wpsimplepay');
  define('MPWPSP_EDITION',MPWPSP_PLUGIN_NAME);
  define('MPWPSP_PATH',WP_PLUGIN_DIR.'/'.MPWPSP_PLUGIN_NAME);
  $mpwpsimplepay_url_protocol = (is_ssl())?'https':'http'; // Make all of our URLS protocol agnostic
  define('MPWPSP_URL',preg_replace('/^https?:/', "{$mpwpsimplepay_url_protocol}:", plugins_url('/'.MPWPSP_PLUGIN_NAME)));

  // Load Addon
  require_once(MPWPSP_PATH . '/MpWPSimplePay.php');
  require_once(MPWPSP_PATH . '/MpWPSimplePayUtils.php');
  new MpWPSimplePay;

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(MPWPSP_PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPWPSP_EDITION,
    MPWPSP_PLUGIN_SLUG,
    'mpwpsimplepay_license_key',
    __('MemberPress WP Simple Pay Pro', 'memberpress-wpsimplepay'),
    __('WP Simple Pay Pro Integration for MemberPress.', 'memberpress-wpsimplepay')
  );
}
