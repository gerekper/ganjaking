<?php
/*
Plugin Name: Slider Revolution Holiday Snow
Plugin URI: http://www.themepunch.com/
Description: Add animated snow to any Slider
Author: ThemePunch
Version: 2.0.0
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-snow-admin'
	'rs-snow-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_SNOW_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_SNOW_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_SNOW_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everyting by calling the following function *
**/
function rs_snow_init(){
	
	new RsSnowBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_snow_init');
register_activation_hook( __FILE__, 'rs_snow_init');

//build js global var for activation
add_filter( 'revslider_activate_addon', array('RsAddOnSnowBase','get_data'),10,2);

// get help definitions on-demand.  merges AddOn definitions with core revslider definitions
add_filter( 'revslider_help_directory', array('RsAddOnSnowBase','get_help'),10,1);

?>