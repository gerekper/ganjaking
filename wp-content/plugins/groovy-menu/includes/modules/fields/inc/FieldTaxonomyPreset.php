<?php

namespace GroovyMenu;

use \GroovyMenuUtils as GroovyMenuUtils;
use \GroovyMenuPreset as GroovyMenuPreset;


defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


/**
 * Class FieldTaxonomyPreset
 */
class FieldTaxonomyPreset extends \GroovyMenu\FieldField {

	/**
	 * Render dashboard front-end
	 */
	public function renderField() {

		$default_arr = array( 'default' => '--- ' . esc_html__( 'Default', 'groovy-menu' ) . ' ---' );
		$none_arr    = array( 'none' => '--- ' . esc_html__( 'Hide Groovy menu', 'groovy-menu' ) . ' ---' );
		$post_types  = GroovyMenuUtils::getPostTypesExtended();
		$value_raw   = $this->getValueRaw();
		$saved_tax   = GroovyMenuUtils::getTaxonomiesPresets( is_string( $value_raw ) ? $value_raw : '', false );
		$nav_menus   = $default_arr + GroovyMenuUtils::getNavMenus();
		$presets     = $default_arr + $none_arr + GroovyMenuPreset::getAll( true );
		$default     =
			empty( $this->getDefault() ) ?
				array(
					'preset' => 'default',
					'menu'   => 'default',
				)
				: $this->getDefault();

		if ( is_string( $value_raw ) && empty( $saved_tax ) ) {
			$value_raw = '';
		}

		?>
		<div class="gm-gui__module__ui gm-gui__module__taxonomy_preset">

			<?php foreach ( $post_types as $type_name => $type_label ) { ?>
				<div class="gm-gui__module__ui gm-gui__subselect-wrapper">
					<p class="gm-gui__module__subselect-title"><?php echo esc_html( $type_label ); ?></p>

					<div class="gm-gui__subselect-wrapper--left">
						<p><?php echo esc_html__( 'Preset', 'groovy-menu' ); ?></p>
						<select data-default="<?php echo esc_attr( $default['preset'] ); ?>"
							data-taxonomy="<?php echo esc_attr( $type_name ); ?>"
							class="gm-subselect gm-subselect--preset">
							<?php foreach ( $presets as $key => $name ) { ?>
								<option
									value="<?php echo esc_attr( $key ); ?>"<?php echo ( isset( $saved_tax[ $type_name ]['preset'] ) && strval( $saved_tax[ $type_name ]['preset'] ) === strval( $key ) ) ? 'selected' : ''; ?>><?php echo esc_html( $name ); ?></option>
							<?php } ?>
						</select>
					</div>

					<div class="gm-gui__subselect-wrapper--right">
						<p><?php echo esc_html__( 'Menu', 'groovy-menu' ); ?></p>
						<select data-default="<?php echo esc_attr( $default['menu'] ); ?>"
							data-taxonomy="<?php echo esc_attr( $type_name ); ?>"
							class="gm-subselect gm-subselect--navmenu">
							<?php foreach ( $nav_menus as $key => $name ) { ?>
								<option
									value="<?php echo esc_attr( $key ); ?>"<?php echo ( isset( $saved_tax[ $type_name ]['menu'] ) && strval( $saved_tax[ $type_name ]['menu'] ) === strval( $key ) ) ? 'selected' : ''; ?>><?php echo esc_html( $name ); ?></option>
							<?php } ?>
						</select>
					</div>

				</div>
			<?php } ?>

			<input type="hidden" class="switch gm-taxonomy_preset"
				value="<?php echo esc_attr( is_string( $value_raw ) ? $value_raw : '' ); ?>"
				name="<?php echo esc_attr( $this->getName() ); ?>" data-default="">

		</div>
		<?php
	}


	/**
	 * Return default field value
	 *
	 * @return null|string
	 */
	public function getDefault() {
		if ( isset( $this->field['default'] ) ) {
			return $this->field['default'];
		}

		return null;
	}

	/**
	 * Get value
	 *
	 * @return array
	 */
	public function getValue() {

		$raw = parent::getValue();

		if ( empty( $raw ) ) {
			return array();
		}

		return GroovyMenuUtils::getTaxonomiesPresets( $raw );
	}


	/**
	 * Get raw value instead URL
	 *
	 * @return string
	 */
	public function getValueRaw() {
		$raw = parent::getValue();

		return $raw;
	}

}
