<?php
/*
Plugin Name: Slider Revolution Panorama AddOn
Plugin URI: http://www.themepunch.com/
Description: Convert Slide Background Images to 360° Panoramas
Author: ThemePunch
Version: 3.0.9
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-panorama-admin'
	'rs-panorama-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_PANORAMA_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_PANORAMA_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_PANORAMA_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everyting by calling the following function *
**/
function rs_panorama_init(){

	new RsPanoramaBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_panorama_init');
register_activation_hook( __FILE__, 'rs_panorama_init');

//build js global var for activation
add_filter( 'revslider_activate_addon', array('RsAddOnPanoramaBase','get_data'),10,2);

// get help definitions on-demand.  merges AddOn definitions with core revslider definitions
add_filter( 'revslider_help_directory', array('RsAddOnPanoramaBase','get_help'),10,1);

?>