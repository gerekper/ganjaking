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
 * FAQ
 *
 * Registers translatable module with items.
 *
 * @since 1.22.0
 */
class FAQ extends WPML_Elementor_Module_With_Items {

	/**
	 * Retrieve the field name.
	 *
	 * @since 1.22.0
	 * @return string
	 */
	public function get_items_field() {
		return 'tabs';
	}

	/**
	 * Retrieve the fields inside the repeater.
	 *
	 * @since 1.22.0
	 *
	 * @return array
	 */
	public function get_fields() {
		return array(
			'question',
			'answer',
		);
	}

	/**
	 * Method for setting the title for each translatable field.
	 *
	 * @since 1.22.0
	 *
	 * @param string $field The name of the field.
	 * @return string
	 */
	protected function get_title( $field ) {
		if ( 'question' === $field ) {
			return __( 'FAQ Schema: Question/Title', 'uael' );
		}
		if ( 'answer' === $field ) {
			return __( 'FAQ Schema: Answer/Content', 'uael' );
		}
		return '';
	}

	/**
	 * Method for determining the editor type for each field.
	 *
	 * @since 1.22.0
	 *
	 * @param  string $field Name of the field.
	 * @return string
	 */
	protected function get_editor_type( $field ) {

		switch ( $field ) {
			case 'question':
			case 'answer':
				return 'LINE';

			default:
				return '';
		}
	}

}
