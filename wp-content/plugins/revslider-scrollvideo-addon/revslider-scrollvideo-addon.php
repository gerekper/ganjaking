<?php
/*
Plugin Name: Slider Revolution Scroll Video Add-On
Plugin URI: http://www.themepunch.com/
Description: Draw and Animate Scrollvideo on Canvas as Layers in Slider Revolution
Author: ThemePunch
Version: 2.0.3
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-scrollvideo-admin'
	'rs-scrollvideo-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_SCROLLVIDEO_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_SCROLLVIDEO_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_SCROLLVIDEO_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everyting by calling the following function *
**/
function rs_scrollvideo_init(){

	new RsScrollvideoBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_scrollvideo_init');
register_activation_hook( __FILE__, 'rs_scrollvideo_init');

//build js global var for activation
add_filter( 'revslider_activate_addon', array('RsAddOnScrollvideoBase','get_data'),10,2);

// get help definitions on-demand.  merges AddOn definitions with core revslider definitions
add_filter( 'revslider_help_directory', array('RsAddOnScrollvideoBase','get_help'),10,1);
		

?>