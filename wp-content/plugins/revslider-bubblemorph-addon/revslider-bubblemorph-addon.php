<?php
/*
Plugin Name: Slider Revolution Bubblemorph AddOn
Plugin URI: http://www.themepunch.com/
Description: Spice up your slides with a Bubble Morph effect
Author: ThemePunch
Version: 3.0.4
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-bubblemorph-admin'
	'rs-bubblemorph-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_BUBBLEMORPH_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_BUBBLEMORPH_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_BUBBLEMORPH_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everyting by calling the following function *
**/
function rs_bubblemorph_init(){

	new RsBubblemorphBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_bubblemorph_init');
register_activation_hook( __FILE__, 'rs_bubblemorph_init');

//build js global var for activation
add_filter( 'revslider_activate_addon', array('RsAddOnBubblemorphBase','get_data'),10,2);

// get help definitions on-demand.  merges AddOn definitions with core revslider definitions
add_filter( 'revslider_help_directory', array('RsAddOnBubblemorphBase','get_help'),10,1);

?>