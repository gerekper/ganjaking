<?php


if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Class WPML_Jet_Elements_Price_List
 */
class WPML_ElementPack_Price_List extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'price_list';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array( 'price', 'title', 'item_description' );
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_title( $field ) {
		switch( $field ) {
			case 'price':
				return esc_html__( 'Price', 'bdthemes-element-pack' );

			case 'title':
				return esc_html__( 'Title', 'bdthemes-element-pack' );

			case 'item_description':
				return esc_html__( 'Description', 'bdthemes-element-pack' );

			default:
				return '';
		}
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'price':
				return 'LINE';

			case 'title':
				return 'LINE';

			case 'item_description':
				return 'AREA';

			default:
				return '';
		}
	}

}
