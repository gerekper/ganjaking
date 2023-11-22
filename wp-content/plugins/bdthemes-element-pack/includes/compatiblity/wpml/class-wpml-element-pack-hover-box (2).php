<?php


if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Class WPML_ElementPack_Hover_Box
 */
class WPML_ElementPack_Hover_Box extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'hover_box';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array( 'hover_box_title', 'hover_box_sub_title', 'hover_box_content', 'hover_box_button' );
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_title( $field ) {
		switch( $field ) {

			case 'hover_box_title':
				return esc_html__( 'Title', 'bdthemes-element-pack' );

			case 'hover_box_sub_title':
				return esc_html__( 'Sub Title', 'bdthemes-element-pack' );

			case 'hover_box_content':
				return esc_html__( 'Content', 'bdthemes-element-pack' );

			case 'hover_box_button':
				return esc_html__( 'Button Text', 'bdthemes-element-pack' );

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
			case 'hover_box_title':
                return 'LINE';

			case 'hover_box_sub_title':
				return 'LINE';

			case 'hover_box_content':
				return 'AREA';

			case 'hover_box_button':
				return 'LINE';

			default:
				return '';
		}
	}

}
