<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Theme Migrate file.
 *
 * @package crane
 */
class GM_migrate__v2_4_8_603 extends GM_Migration {

	/**
	 * Main migrate job
	 *
	 * @return bool
	 */
	public function migrate() {

		$this->db_version = '2.4.8.603';

		// Clear store compiled settings in cache.
		\GroovyMenu\StyleStorage::getInstance()->remove_preset_settings();
		\GroovyMenu\StyleStorage::getInstance()->remove_global_settings();
		\GroovyMenu\StyleStorage::getInstance()->set_disable_storage();

		$presets = \GroovyMenuPreset::getAll();

		foreach ( $presets as $preset ) {

			$preset_id   = $preset->id;
			$preset_name = $preset->name;

			$style      = new \GroovyMenuStyle( $preset_id );
			$preset_obj = new \GroovyMenuPreset( $preset_id );


			$settings = get_post_meta( $preset->id, 'gm_preset_settings', true );
			$settings = json_decode( $settings, true );
			if ( empty( $settings ) || ! is_array( $settings ) ) {
				$settings = array();
			}

			$legal_condition = false;

			if (
				isset( $settings['header'] ) &&
				is_array( $settings['header'] ) &&
				$settings['header']['toolbar'] &&
				1 === $settings['header']['style'] &&
				'center' === $settings['header']['align']
			) {
				$legal_condition = true;
			}

			if ( ! $legal_condition ) {
				continue;
			}

			$this->add_migrate_debug_log( 'set toolbar_align_center as TRUE for preset: ' . $preset_name . ' (id#' . $preset_id . ')' );

			// SET new params.
			$style->set( 'toolbar_align_center', true );

			// Save new params.
			$style->update();

			// Update version.
			update_post_meta( $preset->id, 'gm_version', '2.4.8.603' );
			delete_post_meta( $preset->id, 'gm_compiled_css' );

		}


		$this->success();

		return true;

	}

}