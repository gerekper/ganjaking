<?php
/*
Plugin Name: UserPro (5.1.0)
Secret Key: 83a5bb0e2ad5164690bc7a42ae592cf5
Plugin URI: http://codecanyon.net/user/DeluxeThemes/portfolio?ref=DeluxeThemes
Description: The ultimate user profiles and community plugin for WordPress.
Version: 5.1.0
Author: Deluxe Themes
Author URI: http://codecanyon.net/user/DeluxeThemes/portfolio?ref=DeluxeThemes
*/

// Define UserPro files.
if ( ! defined( 'UP_PLUGIN_FILE' ) ) {
    define( 'UP_PLUGIN_FILE', __FILE__ );
}

// Include the main UserPro file
if ( ! class_exists( 'UserPro' ) ) {
    include_once dirname( __FILE__ ) . '/includes/class-userpro.php';
}

function userpro_init()
{
    return UserPro::instance();
}

userpro_init();

$GLOBALS['userpro'] = new userpro_api();
/* Anti-Leecher Identifier */
/* Credited By BABIATO-FORUM */