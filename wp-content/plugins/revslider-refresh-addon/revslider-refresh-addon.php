<?php
/*
Plugin Name:       Slider Revolution URL Load Add-on
Plugin URI:        https://revolution.themepunch.com
Description:       Load an URL (or reload) after certain amount of time/slider loops/after certain slide
Version:           3.0.2
Author:            ThemePunch
Author URI:        https://www.themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-refresh-admin'
	'rs-refresh-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_REFRESH_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_REFRESH_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_REFRESH_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everyting by calling the following function *
**/
function rs_refresh_init(){

	new RsRefreshBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_refresh_init');
register_activation_hook( __FILE__, 'rs_refresh_init');

//build js global var for activation
add_filter( 'revslider_activate_addon', array('RsAddOnRefreshBase','get_data'),10,2);

// get help definitions on-demand.  merges AddOn definitions with core revslider definitions
add_filter( 'revslider_help_directory', array('RsAddOnRefreshBase','get_help'),10,1);

?>