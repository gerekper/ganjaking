<?php

/**
 * Fired during plugin activation
 *
 * @link       https://wpdeveloper.net
 * @since      1.0.0
 *
 * @package    BetterDocs
 * @subpackage BetterDocs/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    BetterDocs
 * @subpackage BetterDocs/includes
 * @author     WPDeveloper <support@wpdeveloper.net>
 */
class BetterDocs_Activator {

	/**
	 * Detect plugin activation
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		if( current_user_can( 'delete_users' ) ) {
			set_transient( '_betterdocs_meta_activation_notice', true, 30 );
		}
		BetterDocs_Settings::save_default_settings();
	}

}
