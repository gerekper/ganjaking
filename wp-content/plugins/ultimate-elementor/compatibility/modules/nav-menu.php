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
 * Navigation Menu
 *
 * Registers translatable module with items.
 *
 * @since 1.21.0
 */
class Nav_Menu extends WPML_Elementor_Module_With_Items {

	/**
	 * Retrieve the field name.
	 *
	 * @since 1.21.0
	 * @return string
	 */
	public function get_items_field() {

		return 'menu_items';
	}

	/**
	 * Retrieve the fields inside the repeater.
	 *
	 * @since 1.21.0
	 *
	 * @return array
	 */
	public function get_fields() {
		return array(
			'text',
			'link' => array( 'url' ),
		);
	}

	/**
	 * Method for setting the title for each translatable field.
	 *
	 * @since 1.21.0
	 *
	 * @param string $field The name of the field.
	 * @return string
	 */
	protected function get_title( $field ) {
		if ( 'text' === $field ) {
			return __( 'Navigation Menu: Text', 'uael' );
		}

		if ( 'link' === $field ) {
			return __( 'Navigation Menu: Link', 'uael' );
		}

		return '';
	}

	/**
	 * Method for determining the editor type for each field.
	 *
	 * @since 1.21.0
	 *
	 * @param  string $field Name of the field.
	 * @return string
	 */
	protected function get_editor_type( $field ) {

		switch ( $field ) {
			case 'text':
			case 'link':
				return 'LINE';

			default:
				return '';
		}
	}

}
