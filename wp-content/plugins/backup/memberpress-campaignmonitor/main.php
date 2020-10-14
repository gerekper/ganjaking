<?php
/*
Plugin Name: MemberPress Campaign Monitor
Plugin URI: http://www.memberpress.com/
Description: Campaign Monitor Autoresponder integration for MemberPress.
Version: 1.0.2
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress-campaignmonitor
Copyright: 2004-2015, Caseproof, LLC
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if(is_plugin_active('memberpress/memberpress.php')) {

  define('MPCAMPAIGNMONITOR_PLUGIN_SLUG','memberpress-campaignmonitor/main.php');
  define('MPCAMPAIGNMONITOR_PLUGIN_NAME','memberpress-campaignmonitor');
  define('MPCAMPAIGNMONITOR_EDITION',MPCAMPAIGNMONITOR_PLUGIN_NAME);
  define('MPCAMPAIGNMONITOR_PATH',WP_PLUGIN_DIR.'/'.MPCAMPAIGNMONITOR_PLUGIN_NAME);
  $mpcampaignmonitor_url_protocol = (is_ssl())?'https':'http'; // Make all of our URLS protocol agnostic
  define('MPCAMPAIGNMONITOR_URL',preg_replace('/^https?:/', "{$mpcampaignmonitor_url_protocol}:", plugins_url('/'.MPCAMPAIGNMONITOR_PLUGIN_NAME)));

  // Load Addon
  require_once(MPCAMPAIGNMONITOR_PATH . '/MpCampaignMonitor.php');
  new MpCampaignMonitor;

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(MPCAMPAIGNMONITOR_PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPCAMPAIGNMONITOR_EDITION,
    MPCAMPAIGNMONITOR_PLUGIN_SLUG,
    'mpcampaignmonitor_license_key',
    __('MemberPress Campaign Monitor', 'memberpress-campaignmonitor'),
    __('Campaign Monitor Autoresponder Integration for MemberPress.', 'memberpress-campaignmonitor')
  );

}

