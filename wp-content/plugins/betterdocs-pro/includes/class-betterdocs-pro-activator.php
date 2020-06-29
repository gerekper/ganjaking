<?php

/**
 * Fired during plugin activation
 *
 * @link       https://wpdeveloper.net
 * @since      1.0.0
 *
 * @package    Betterdocs_Pro
 * @subpackage Betterdocs_Pro/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Betterdocs_Pro
 * @subpackage Betterdocs_Pro/includes
 * @author     WPDeveloper <support@wpdeveloper.net>
 */
class Betterdocs_Pro_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		/**
		 * Free installer 
		 */
		require_once BETTERDOCS_PRO_ADMIN_DIR_PATH . 'includes/class-betterdocs-installer.php';
		new BetterDocs_Installer();
	}

}
