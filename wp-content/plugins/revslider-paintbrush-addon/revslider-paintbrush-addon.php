<?php
/*
Plugin Name: Slider Revolution Paint-Brush Add-On
Plugin URI: http://www.themepunch.com/
Description: Draw images onto your slides on-mouse-move
Author: ThemePunch
Version: 2.1.6
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-paintbrush-admin'
	'rs-paintbrush-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_PAINTBRUSH_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_PAINTBRUSH_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_PAINTBRUSH_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everyting by calling the following function *
**/
function rs_paintbrush_init(){

	new RsPaintbrushBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_paintbrush_init');
register_activation_hook( __FILE__, 'rs_paintbrush_init');

//build js global var for activation
add_filter( 'revslider_activate_addon', array('RsAddOnPaintbrushBase','get_data'),10,2);

// get help definitions on-demand.  merges AddOn definitions with core revslider definitions
add_filter( 'revslider_help_directory', array('RsAddOnPaintbrushBase','get_help'),10,1);

?>