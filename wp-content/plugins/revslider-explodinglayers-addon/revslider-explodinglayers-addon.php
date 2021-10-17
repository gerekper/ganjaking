<?php
/*
Plugin Name: Slider Revolution Exploding Layers Add-On
Plugin URI: http://www.themepunch.com/
Description: Animate your RevSlider Layers with Particles
Author: ThemePunch
Version: 3.0.4
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-explodinglayers-admin'
	'rs-explodinglayers-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_EXPLODINGLAYERS_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_EXPLODINGLAYERS_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_EXPLODINGLAYERS_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everyting by calling the following function *
**/
function rs_explodinglayers_init(){

	new RsExplodinglayersBase();

}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_explodinglayers_init');
register_activation_hook( __FILE__, 'rs_explodinglayers_init');


//build js global var for activation
add_filter( 'revslider_activate_addon', array('RsAddOnExplodinglayersBase','get_var'),10,2);

// get help definitions on-demand.  merges AddOn definitions with core revslider definitions
add_filter( 'revslider_help_directory', array('RsAddOnExplodinglayersBase','get_help'),10,1);

?>