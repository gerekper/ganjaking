<?php
/*
Plugin Name: Slider Revolution Distortion Effect AddOn
Plugin URI: http://www.themepunch.com/
Description: Enhance your slides with distortion effects
Author: ThemePunch
Version: 3.0.3
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-liquideffect-admin'
	'rs-liquideffect-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_LIQUIDEFFECT_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_LIQUIDEFFECT_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_LIQUIDEFFECT_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everyting by calling the following function *
**/
function rs_liquideffect_init(){

	new RsLiquideffectBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_liquideffect_init');
register_activation_hook( __FILE__, 'rs_liquideffect_init');

//build js global var for activation
add_filter( 'revslider_activate_addon', array('RsAddOnLiquideffectBase','get_data'),10,2);

// get help definitions on-demand.  merges AddOn definitions with core revslider definitions
add_filter( 'revslider_help_directory', array('RsAddOnLiquideffectBase','get_help'),10,1);

?>