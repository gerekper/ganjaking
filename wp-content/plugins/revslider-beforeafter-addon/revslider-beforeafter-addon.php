<?php
/*
Plugin Name: Slider Revolution Before/After Add-On
Plugin URI: https://www.themepunch.com/
Description: Create Before/After content for your Slides
Author: ThemePunch
Version: 2.1.0
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-beforeafter-admin'
	'rs-beforeafter-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_BEFOREAFTER_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_BEFOREAFTER_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_BEFOREAFTER_PLUGIN_PATH . 'includes/base.class.php');


/**
* handle everyting by calling the following function *
**/
function rs_beforeafter_init(){

	new RsBeforeAfterBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_beforeafter_init');
register_activation_hook( __FILE__, 'rs_beforeafter_init');

//build js global var for activation
add_filter( 'revslider_activate_addon', array('RsAddOnBeforeAfterBase','get_data'),10,2);

// get help definitions on-demand.  merges AddOn definitions with core revslider definitions
add_filter( 'revslider_help_directory', array('RsAddOnBeforeAfterBase','get_help'),10,1);

?>