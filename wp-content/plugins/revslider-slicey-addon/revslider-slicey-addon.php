<?php
/*
Plugin Name: Slider Revolution Slicey Add-On
Plugin URI: http://www.themepunch.com/
Description: Display a continously sliding set of images for your slide backgrounds
Author: ThemePunch
Version: 2.0.4
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-slicey-admin'
	'rs-slicey-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_SLICEY_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_SLICEY_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_SLICEY_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everyting by calling the following function *
**/
function rs_slicey_init(){

	new RsSliceyBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_slicey_init');
register_activation_hook( __FILE__, 'rs_slicey_init');

//build js global var for activation
add_filter( 'revslider_activate_addon', array('RsAddOnSliceyBase','get_data'),10,2);

// get help definitions on-demand.  merges AddOn definitions with core revslider definitions
add_filter( 'revslider_help_directory', array('RsAddOnSliceyBase','get_help'),10,1);
		

?>