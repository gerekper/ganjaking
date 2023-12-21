<?php
namespace Elementor\Core\Settings\General;

use Happy_Addons_Pro\Live_Copy;

defined( 'ABSPATH' ) || die();

class HA_General_Settings extends Manager {

	/**
	 * Get saved settings.
	 *
	 * Retrieve the saved settings from the site options.
	 *
	 * @since 1.6.0
	 * @access protected
	 *
	 * @param int $id Post ID.
	 *
	 * @return array Saved settings.
	 */
	protected function get_saved_settings( $id ) {
		$settings = parent::get_saved_settings( $id );
		return Live_Copy::get_control_settings( $settings );
	}

	/**
	 * Save settings to DB.
	 *
	 * Save general settings to the database, as site options.
	 *
	 * @since 1.6.0
	 * @access protected
	 *
	 * @param array $settings Settings.
	 * @param int   $id       Post ID.
	 */
	protected function save_settings_to_db( array $settings, $id ) {
		parent::save_settings_to_db( $settings, $id );
		Live_Copy::save_control_settings( $settings, $id );
	}
}
