<?php
/*
Plugin Name: MemberPress Math CAPTCHA
Plugin URI: http://memberpress.com
Description: Shows a Math question on the signup forms to prevent bots from signing up SPAM accounts.
Version: 1.1.7
Author: Caseproof, LLC
Author URI: http://caseproof.com
Text Domain: memberpress-math-captcha
Copyright: 2004-2020, Caseproof, LLC
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

include_once( ABSPATH . 'wp-admin/includes/plugin.php');

if(is_plugin_active('memberpress/memberpress.php')) {
  define('MPMATH_PLUGIN_SLUG', 'memberpress-math-captcha/main.php');
  define('MPMATH_PLUGIN_NAME', 'memberpress-math-captcha');
  define('MPMATH_PATH', WP_PLUGIN_DIR.'/'.MPMATH_PLUGIN_NAME);
  define('MPMATH_URL', plugins_url('/'.MPMATH_PLUGIN_NAME));
  define('MPMATH_I18N_PATH', MPMATH_PATH . '/i18n');
  define('MPMATH_DB_KEY', 'meprmath_unique_key');
  define('MPMATH_EDITION', MPMATH_PLUGIN_NAME);

  // Load Addon
  require_once(MPMATH_PATH . '/MpMathCaptcha.php');
  new MpMathCaptcha;

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(MPMATH_PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPMATH_EDITION,
    MPMATH_PLUGIN_SLUG,
    'mpmathcaptcha_license_key',
    __('MemberPress Math Captcha', 'memberpress-math-captcha'),
    __('Math Captcha add-on for MemberPress.', 'memberpress-math-captcha')
  );
}
