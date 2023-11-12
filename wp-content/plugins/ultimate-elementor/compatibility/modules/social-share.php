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
 * Buttons
 *
 * Registers translatable module with items.
 *
 * @since 1.30.0
 */
class SocialShare extends WPML_Elementor_Module_With_Items {

	/**
	 * Retrieve the field name.
	 *
	 * @since 1.30.0
	 * @return string
	 */
	public function get_items_field() {
		return 'social_icon_list';
	}

	/**
	 * Retrieve the fields inside the repeater.
	 *
	 * @since 1.30.0
	 *
	 * @return array
	 */
	public function get_fields() {
		return array(
			'Custom_text',
		);
	}

	/**
	 * Method for setting the title for each translatable field.
	 *
	 * @since 1.30.0
	 *
	 * @param string $field The name of the field.
	 * @return string
	 */
	protected function get_title( $field ) {

		if ( 'Custom_text' === $field ) {
			return __( 'Social Share: Custom Label', 'uael' );
		}

		return '';
	}

	/**
	 * Method for determining the editor type for each field.
	 *
	 * @since 1.30.0
	 *
	 * @param  string $field Name of the field.
	 * @return string
	 */
	protected function get_editor_type( $field ) {

		switch ( $field ) {
			case 'Custom_text':
				return 'LINE';

			default:
				return '';
		}
	}
}
