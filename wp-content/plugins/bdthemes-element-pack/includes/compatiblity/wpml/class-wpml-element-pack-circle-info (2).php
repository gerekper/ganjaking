<?php


if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Class WPML_ElementPack_Circle_Info
 */
class WPML_ElementPack_Circle_Info extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'circle_info_icon_list';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array( 'circle_info_item_title', 'circle_info_item_details' );
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_title( $field ) {
		switch( $field ) {

			case 'circle_info_item_title':
				return esc_html__( 'Title', 'bdthemes-element-pack' );

			case 'circle_info_item_details':
				return esc_html__( 'Details', 'bdthemes-element-pack' );

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
			case 'circle_info_item_title':
				return 'LINE';

			case 'circle_info_item_details':
				return 'AREA';

			default:
				return '';
		}
	}

}
