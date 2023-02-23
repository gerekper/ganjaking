<?php
/*
Plugin Name: Slider Revolution Mousetrap Add-On
Plugin URI: http://www.themepunch.com/
Description: Draw and Animate Mousetrap on Canvas as Layers in Slider Revolution
Author: ThemePunch
Version: 3.0.7
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-mousetrap-admin'
	'rs-mousetrap-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_MOUSETRAP_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_MOUSETRAP_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_MOUSETRAP_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everyting by calling the following function *
**/
function rs_mousetrap_init(){

	new RsMousetrapBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_mousetrap_init');
register_activation_hook( __FILE__, 'rs_mousetrap_init');

//build js global var for activation
add_filter( 'revslider_activate_addon', array('RsAddOnMousetrapBase','get_data'),10,2);

// get help definitions on-demand.  merges AddOn definitions with core revslider definitions
add_filter( 'revslider_help_directory', array('RsAddOnMousetrapBase','get_help'),10,1);
		

?>