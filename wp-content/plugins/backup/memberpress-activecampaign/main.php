<?php
/*
Plugin Name: MemberPress Active Campaign
Plugin URI: http://www.memberpress.com/
Description: Active Campaign Autoresponder integration for MemberPress.
Version: 1.0.4
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress-activecampaign
Copyright: 2004-2015, Caseproof, LLC
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

include_once(ABSPATH . 'wp-admin/includes/plugin.php');
if(is_plugin_active('memberpress/memberpress.php')) {

  define('MPACTIVECAMPAIGN_PLUGIN_SLUG','memberpress-activecampaign/main.php');
  define('MPACTIVECAMPAIGN_PLUGIN_NAME','memberpress-activecampaign');
  define('MPACTIVECAMPAIGN_EDITION',MPACTIVECAMPAIGN_PLUGIN_NAME);
  define('MPACTIVECAMPAIGN_PATH',WP_PLUGIN_DIR.'/'.MPACTIVECAMPAIGN_PLUGIN_NAME);
  $mpactivecampaign_url_protocol = (is_ssl())?'https':'http'; // Make all of our URLS protocol agnostic
  define('MPACTIVECAMPAIGN_URL',preg_replace('/^https?:/', "{$mpactivecampaign_url_protocol}:", plugins_url('/'.MPACTIVECAMPAIGN_PLUGIN_NAME)));

  // Load Addon
  require_once(MPACTIVECAMPAIGN_PATH . '/MpActiveCampaign.php');
  new MpActiveCampaign;

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(MPACTIVECAMPAIGN_PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPACTIVECAMPAIGN_EDITION,
    MPACTIVECAMPAIGN_PLUGIN_SLUG,
    'mpactivecampaign_license_key',
    __('MemberPress Active Campaign', 'memberpress-activecampaign'),
    __('Active Campaign Autoresponder Integration for MemberPress.', 'memberpress-activecampaign')
  );

}

