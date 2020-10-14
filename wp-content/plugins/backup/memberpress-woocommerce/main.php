<?php
/*
Plugin Name: MemberPress WooCommerce
Plugin URI: http://www.memberpress.com/
Description: WooCommerce integration for MemberPress.
Version: 1.0.5
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress-woocommerce
Copyright: 2004-2018, Caseproof, LLC
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

if(is_plugin_active('memberpress/memberpress.php')) {
  define('MPWOO_PLUGIN_SLUG','memberpress-woocommerce/main.php');
  define('MPWOO_PLUGIN_NAME','memberpress-woocommerce');
  define('MPWOO_EDITION',MPWOO_PLUGIN_NAME);
  define('MPWOO_PATH',WP_PLUGIN_DIR.'/'.MPWOO_PLUGIN_NAME);
  $mpwoocommerce_url_protocol = (is_ssl())?'https':'http'; // Make all of our URLS protocol agnostic
  define('MPWOO_URL',preg_replace('/^https?:/', "{$mpwoocommerce_url_protocol}:", plugins_url('/'.MPWOO_PLUGIN_NAME)));

  // Load Addon
  require_once(MPWOO_PATH . '/MpWooCommerce.php');
  new MpWooCommerce;

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(MPWOO_PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPWOO_EDITION,
    MPWOO_PLUGIN_SLUG,
    'mpwoocommerce_license_key',
    __('MemberPress WooCommerce', 'memberpress-woocommerce'),
    __('WooCommerce Integration for MemberPress.', 'memberpress-woocommerce')
  );
}
