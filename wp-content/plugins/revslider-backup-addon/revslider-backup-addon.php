<?php
/*
Plugin Name: Slider Revolution Backup Add-On
Plugin URI: http://www.themepunch.com/
Description: Add Backup-Filters to your Slider Images
Author: ThemePunch
Version: 2.0.2
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-backup-admin'
	'rs-backup-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_BACKUP_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_BACKUP_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_BACKUP_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everyting by calling the following function *
**/
function rs_backup_init(){

	new RsBackupBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_backup_init');
register_activation_hook( __FILE__, 'rs_backup_init');

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array('RsAddOnBackupBase', 'create_tables' ));

?>