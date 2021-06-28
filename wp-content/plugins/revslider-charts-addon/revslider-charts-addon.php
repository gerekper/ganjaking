<?php
/*
Plugin Name: Slider Revolution Charts AddOn
Plugin URI: http://www.themepunch.com/
Description: Add charts to your website that make your data look awesome.
Author: ThemePunch
Version: 3.0.1
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-charts-admin'
	'rs-charts-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_CHARTS_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_CHARTS_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_CHARTS_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everyting by calling the following function *
**/
function rs_charts_init(){

	new RsChartsBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_charts_init');
register_activation_hook( __FILE__, 'rs_charts_init');

//build js global var for activation
add_filter( 'revslider_activate_addon', array('RsAddOnChartsBase','get_data'),10,2);

// get help definitions on-demand.  merges AddOn definitions with core revslider definitions
add_filter( 'revslider_help_directory', array('RsAddOnChartsBase','get_help'),10,1);

?>