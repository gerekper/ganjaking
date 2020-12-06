<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Theme Migrate file.
 *
 * @package crane
 */
class GM_migrate__v1_4_4_403 extends GM_Migration {

	/**
	 * Migrate
	 *
	 * @return bool
	 */
	public function migrate() {

		$this->db_version = '1.4.4.403';

		$preset_change_id = get_option( 'gm_migration_data_1_4_4_400_1' );

		if ( empty( $preset_change_id ) || ! is_array( $preset_change_id ) ) {
			$preset_change_id = GroovyMenuUtils::get_old_preset_ids();
		}

		foreach ( $preset_change_id as $old_id => $new_id ) {
			delete_option( 'groovy_menu_settings_preset_' . $old_id );
			delete_option( 'groovy_menu_settings_preview_' . $old_id );
			delete_option( 'groovy_menu_settings_thumb_' . $old_id );
			delete_option( 'groovy_menu_settings_screenshot_' . $old_id );
		}


		global $wpdb;
		$table_name = $wpdb->prefix . 'groovy_preset';
		$sql        = "DROP TABLE IF EXISTS $table_name";
		$wpdb->query( $sql );


		delete_option( 'gm_migration_data_1_4_4_400_1' );


		$this->success();

		return true;

	}

}
