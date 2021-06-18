<?php
/*
Plugin Name: Slider Revolution Polyfold Scroll Effect
Plugin URI: http://www.themepunch.com/
Description: Add sharp edges to your sliders as they scroll into and out of view
Author: ThemePunch
Version: 3.0.1
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-polyfold-admin'
	'rs-polyfold-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_POLYFOLD_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_POLYFOLD_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_POLYFOLD_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everyting by calling the following function *
**/
function rs_polyfold_init(){

	new RsPolyfoldBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_polyfold_init');
register_activation_hook( __FILE__, 'rs_polyfold_init');

//build js global var for activation
add_filter( 'revslider_activate_addon', array('RsAddOnPolyfoldBase','get_data'),10,2);

// get help definitions on-demand.  merges AddOn definitions with core revslider definitions
add_filter( 'revslider_help_directory', array('RsAddOnPolyfoldBase','get_help'),10,1);

?>