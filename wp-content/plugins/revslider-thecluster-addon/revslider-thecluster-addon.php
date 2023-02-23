<?php
/*
Plugin Name: Slider Revolution The Cluster AddOn
Plugin URI: http://www.themepunch.com/
Description: Add mind blowing Cluster particle effect you your Slider Revolution layers
Author: ThemePunch
Version: 1.0.5
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-thecluster-admin'
	'rs-thecluster-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_THECLUSTER_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_THECLUSTER_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_THECLUSTER_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everyting by calling the following function *
**/
function rs_thecluster_init(){

	new RsTheClusterBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_thecluster_init');
register_activation_hook( __FILE__, 'rs_thecluster_init');

//build js global var for activation
add_filter( 'revslider_activate_addon', array('RsAddOnTheClusterBase','get_data'),10,2);

// get help definitions on-demand.  merges AddOn definitions with core revslider definitions
add_filter( 'revslider_help_directory', array('RsAddOnTheClusterBase','get_help'),10,1);

?>