<?php


if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Class WPML_ElementPack_Image_Accordion
 */
class WPML_ElementPack_Image_Accordion extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'image_accordion_items';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array( 'image_accordion_title', 'image_accordion_sub_title', 'image_accordion_text', 'image_accordion_button' );
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_title( $field ) {
		switch( $field ) {

			case 'image_accordion_title':
				return esc_html__( 'Title', 'bdthemes-element-pack' );

			case 'image_accordion_sub_title':
				return esc_html__( 'Sub Title', 'bdthemes-element-pack' );

			case 'image_accordion_text':
				return esc_html__( 'Content', 'bdthemes-element-pack' );

			case 'image_accordion_button':
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
			case 'image_accordion_title':
                return 'LINE';

			case 'image_accordion_sub_title':
				return 'LINE';

			case 'image_accordion_text':
				return 'AREA';

			case 'image_accordion_button':
				return 'LINE';

			default:
				return '';
		}
	}

}
