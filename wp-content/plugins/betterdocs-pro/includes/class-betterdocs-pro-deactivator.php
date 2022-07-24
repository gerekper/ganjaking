<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://wpdeveloper.com
 * @since      1.0.0
 *
 * @package    Betterdocs_Pro
 * @subpackage Betterdocs_Pro/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Betterdocs_Pro
 * @subpackage Betterdocs_Pro/includes
 * @author     WPDeveloper <support@wpdeveloper.com>
 */
class Betterdocs_Pro_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
        flush_rewrite_rules();

		if( ! class_exists('BetterDocs_Role_Management') ){
			require_once plugin_dir_path(dirname(__FILE__)) . 'admin/includes/class-betterdocs-role-management.php';
		}

		BetterDocs_Role_Management::remove_caps();
	}

}
