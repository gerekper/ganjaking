<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Theme Migrate file.
 *
 * @package crane
 */
class GM_migrate__v1_1_4 extends GM_Migration {

	/**
	 * @return bool
	 */
	function migrate() {

		$this->db_version = '1.1.4';


		$presets = GroovyMenuPreset::getAll();

		foreach ( $presets as $preset ) {
			$style                               = new GroovyMenuStyle( $preset->id );
			$sticky_top_level_text_color_hover_2 = $style->get( 'styles', 'sticky_top_level_text_color_hover_2' );
			if ( '#93cb52' !== $sticky_top_level_text_color_hover_2 ) {
				continue;
			}

			$style->set( 'sticky_top_level_text_color_hover_2', '#ffffff' );
			$style->update();
		}


		$this->success();

		return true;

	}

}
