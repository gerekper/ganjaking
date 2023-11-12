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
class PriceList extends WPML_Elementor_Module_With_Items {

	/**
	 * Retrieve the field name.
	 *
	 * @since 1.2.2
	 * @return string
	 */
	public function get_items_field() {
		return 'price_list';
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
			'title',
			'item_description',
			'price',
			'original_price',
			'link' => array( 'url' ),
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
		if ( 'title' === $field ) {
			return __( 'Price List: Title', 'uael' );
		}

		if ( 'item_description' === $field ) {
			return __( 'Price List: Description', 'uael' );
		}

		if ( 'price' === $field ) {
			return __( 'Price List: Price', 'uael' );
		}

		if ( 'original_price' === $field ) {
			return __( 'Price List: Original Price', 'uael' );
		}

		if ( 'url' === $field ) {
			return __( 'Price List: Item Link', 'uael' );
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
			case 'map_title':
			case 'title':
			case 'price':
			case 'original_price':
			case 'url':
				return 'LINE';

			case 'item_description':
				return 'AREA';

			default:
				return '';
		}
	}

}
