<?php
/*
Plugin Name: Slider Revolution Reveal Preloaders
Plugin URI: http://www.themepunch.com/
Description: Reveal your sliders in style
Author: ThemePunch
Version: 2.0.0
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-revealer-admin'
	'rs-revealer-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_REVEALER_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_REVEALER_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_REVEALER_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everyting by calling the following function *
**/
function rs_revealer_init(){

	new RsRevealerBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_revealer_init');
register_activation_hook( __FILE__, 'rs_revealer_init');

//build js global var for activation
add_filter( 'revslider_activate_addon', array('RsAddOnRevealerBase','get_data'),10,2);

// get help definitions on-demand.  merges AddOn definitions with core revslider definitions
add_filter( 'revslider_help_directory', array('RsAddOnRevealerBase','get_help'),10,1);

?>