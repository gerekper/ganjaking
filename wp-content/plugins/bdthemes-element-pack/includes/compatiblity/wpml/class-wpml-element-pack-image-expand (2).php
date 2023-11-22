<?php


if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Class WPML_ElementPack_Image_Expand
 */
class WPML_ElementPack_Image_Expand extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'image_expand_items';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array( 'image_expand_title', 'image_expand_sub_title', 'image_expand_text', 'image_expand_button' );
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_title( $field ) {
		switch( $field ) {

			case 'image_expand_title':
				return esc_html__( 'Title', 'bdthemes-element-pack' );

			case 'image_expand_sub_title':
				return esc_html__( 'Sub Title', 'bdthemes-element-pack' );

			case 'image_expand_text':
				return esc_html__( 'Content', 'bdthemes-element-pack' );

			case 'image_expand_button':
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
			case 'image_expand_title':
                return 'LINE';

			case 'image_expand_sub_title':
				return 'LINE';

			case 'image_expand_text':
				return 'AREA';

			case 'image_expand_button':
				return 'LINE';

			default:
				return '';
		}
	}

}
