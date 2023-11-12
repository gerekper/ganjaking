<?php
/**
 * UAEL WPML compatibility.
 *
 * @package UAEL
 */

namespace UltimateElementor\Compatibility\WPML;

use WPML_Elementor_Module_With_Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Table
 *
 * Registers translatable module with items.
 *
 * @since 1.2.2
 */
class PriceTable extends WPML_Elementor_Module_With_Items {

	/**
	 * Retrieve the field name.
	 *
	 * @since 1.2.2
	 * @return string
	 */
	public function get_items_field() {
		return 'features_list';
	}

	/**
	 * Retrieve the fields inside the repeater.
	 *
	 * @since 1.2.2
	 *
	 * @return array
	 */
	public function get_fields() {
		return array(
			'item_text',
			'tooltip_content',
		);
	}

	/**
	 * Method for setting the title for each translatable field.
	 *
	 * @since 1.2.2
	 *
	 * @param string $field The name of the field.
	 * @return string
	 */
	protected function get_title( $field ) {
		if ( 'item_text' === $field ) {
			return __( 'Price Table: Feature Title', 'uael' );
		}

		if ( 'tooltip_content' === $field ) {
			return __( 'Price Table: Tooltip Content', 'uael' );
		}

		return '';
	}

	/**
	 * Method for determining the editor type for each field.
	 *
	 * @since 1.2.2
	 *
	 * @param  string $field Name of the field.
	 * @return string
	 */
	protected function get_editor_type( $field ) {

		switch ( $field ) {
			case 'item_text':
				return 'LINE';

			case 'tooltip_content':
				return 'VISUAL';

			default:
				return '';
		}
	}

}
