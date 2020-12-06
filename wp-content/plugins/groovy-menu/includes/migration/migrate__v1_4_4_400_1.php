<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Theme Migrate file.
 *
 * @package crane
 */
class GM_migrate__v1_4_4_400_1 extends GM_Migration {

	/**
	 * Migrate
	 *
	 * @return bool
	 */
	public function migrate() {

		$this->db_version = '1.4.4.400.1';


		GroovyMenuStyleStorage::getInstance()->set_disable_storage();

		$presets        = GroovyMenuPreset::getAll();
		$timer_shift    = count( $presets ) + 5;
		$migration_data = array();

		$this->add_migrate_debug_log( 'Start migrate old preset type to new preset [groovy_menu_preset]' );

		foreach ( $presets as $preset ) {

			$preset_id       = $preset->id;
			$preset_name     = $preset->name;
			$style           = new GroovyMenuStyle( $preset_id );
			$preset_settings = $style->serialize( true, false, false, false );
			$compiled_css    = '';
			$direction       = '';
			$version         = '';

			if ( ! empty( $preset_settings['compiled_css'] ) ) {
				$compiled_css = $preset_settings['compiled_css'];
				unset( $preset_settings['compiled_css'] );
			}
			if ( ! empty( $preset_settings['direction'] ) ) {
				$direction = $preset_settings['direction'];
			}
			if ( ! empty( $preset_settings['version'] ) ) {
				$version = $preset_settings['version'];
			}

			$preset_settings = wp_json_encode( $preset_settings );
			$preset_preview  = GroovyMenuPreset::getPreviewById( $preset_id );
			$preset_thumb    = GroovyMenuPreset::getThumb( $preset_id );

			if ( strlen( $preset_preview ) > 1024 || 'api.groovy.grooni.com' !== wp_parse_url( $preset_preview, PHP_URL_HOST ) ) {
				$preset_preview = '';
			}

			$post_meta = array(
				'gm_preset_settings' => $preset_settings,
				'gm_preset_preview'  => $preset_preview,
				'gm_preset_thumb'    => $preset_thumb,
				'gm_compiled_css'    => $compiled_css,
				'gm_direction'       => $direction,
				'gm_version'         => $version,
				'gm_old_id'          => $preset_id,
			);

			$new_post_args = array(
				'post_author'  => 1,
				'post_content' => '',
				'post_excerpt' => '',
				'post_name'    => $preset_name,
				'post_status'  => 'publish',
				'post_title'   => $preset_name,
				'post_type'    => 'groovy_menu_preset',
				'meta_input'   => $post_meta,
				'post_date'    => date( 'Y-m-d H:i:s', intval( current_time( 'timestamp' ) ) - $timer_shift ),
			);

			$timer_shift --;


			$this->add_migrate_debug_log( 'inset new post for preset: ' . $preset_name . ' (id#' . $preset_id . ')' );

			// Inset post.
			$new_post_id = wp_insert_post( $new_post_args );

			$this->add_migrate_debug_log( 'new post id of [groovy_menu_preset]: ' . $new_post_id );

			$migration_data[ $preset_id ] = $new_post_id;

		}


		update_option( 'gm_migration_data_1_4_4_400_1', $migration_data, false );

		// update taxonomies settings for Global Settings.
		$this->add_migrate_debug_log( 'Start update taxonomies settings for Global Settings' );
		$style           = new GroovyMenuStyle();
		$global_settings = get_option( GroovyMenuStyle::OPTION_NAME );

		$saved_tax = GroovyMenuUtils::getTaxonomiesPresets();

		foreach ( $saved_tax as $post_type => $settings ) {
			if ( ! empty( $settings['preset'] ) ) {
				$old_val = intval( $settings['preset'] );
				if ( isset( $migration_data[ $old_val ] ) ) {
					$saved_tax[ $post_type ]['preset'] = strval( $migration_data[ $old_val ] );
				}
			}
		}

		$new_saved_tax_string                               = GroovyMenuUtils::setTaxonomiesPresets( $saved_tax );
		$global_settings['taxonomies']['taxonomies_preset'] = $new_saved_tax_string;

		// Update settings.
		$style->updateGlobal( $global_settings );

		// Update used_in.
		$used_in_storage = get_option( 'groovy_menu_preset_used_in_storage' );
		if ( empty( $used_in_storage ) ) {
			$used_in_storage = array();
		}

		foreach ( $saved_tax as $post_type => $settings ) {
			$_preset_id = empty( $settings['preset'] ) ? '' : intval( $settings['preset'] );
			if ( ! empty( $_preset_id ) ) {
				$used_in_storage['global'][ $post_type ] = $_preset_id;
			}
		}

		if ( ! empty( $used_in_storage ) ) {
			update_option( 'groovy_menu_preset_used_in_storage', $used_in_storage, false );
		}


		$this->success();

		return true;

	}

}
