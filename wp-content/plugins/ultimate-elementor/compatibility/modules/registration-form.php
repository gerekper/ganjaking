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
 * Registration Form
 *
 * Registers translatable module with items.
 *
 * @since 1.21.1
 */
class RegistrationForm extends WPML_Elementor_Module_With_Items {

	/**
	 * Retrieve the field name.
	 *
	 * @since 1.21.1
	 * @return string
	 */
	public function get_items_field() {
		return 'fields_list';
	}

	/**
	 * Retrieve the fields inside the repeater.
	 *
	 * @since 1.21.1
	 *
	 * @return array
	 */
	public function get_fields() {
		return array(
			'field_label',
			'placeholder',
		);
	}

	/**
	 * Method for setting the title for each translatable field.
	 *
	 * @since 1.21.1
	 *
	 * @param string $field The name of the field.
	 * @return string
	 */
	protected function get_title( $field ) {
		if ( 'field_label' === $field ) {
			return __( 'Registration Form: Field Label', 'uael' );
		}

		if ( 'placeholder' === $field ) {
			return __( 'Registration Form: Placeholder', 'uael' );
		}

		return '';
	}

	/**
	 * Method for determining the editor type for each field.
	 *
	 * @since 1.21.1
	 *
	 * @param  string $field Name of the field.
	 * @return string
	 */
	protected function get_editor_type( $field ) {

		switch ( $field ) {
			case 'field_label':
			case 'placeholder':
				return 'LINE';

			default:
				return '';
		}
	}

}
