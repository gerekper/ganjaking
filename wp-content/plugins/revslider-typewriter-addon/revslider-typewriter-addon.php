<?php
/*
Plugin Name: Slider Revolution Typewriter Effect
Plugin URI: http://www.themepunch.com/
Description: Enhance your slider's text with typewriter effects
Author: ThemePunch
Version: 3.0.5
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-typewriter-admin'
	'rs-typewriter-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_TYPEWRITER_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_TYPEWRITER_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));
define('RS_TYPEWRITER_VERSION', '3.0.5');

require_once(RS_TYPEWRITER_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everyting by calling the following function *
**/
function rs_typewriter_init(){
	
	new RsTypewriterBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_typewriter_init');
register_activation_hook( __FILE__, 'rs_typewriter_init');

//build js global var for activation
add_filter( 'revslider_activate_addon', array('RsAddOnBase','get_data'),10,2);

// get help definitions on-demand.  merges AddOn definitions with core revslider definitions
add_filter( 'revslider_help_directory', array('RsAddOnBase','get_help'),10,1);

?>