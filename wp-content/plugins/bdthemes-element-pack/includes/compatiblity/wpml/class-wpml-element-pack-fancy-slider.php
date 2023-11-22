<?php



if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Class WPML_ElementPack_Fancy_Slider
 */
class WPML_ElementPack_Fancy_Slider extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'slides';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array( 'sub_title', 'title', 'description', 'slide_button' );
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_title( $field ) {
		switch( $field ) {

			case 'sub_title':
				return esc_html__( 'Sub Title', 'bdthemes-element-pack' );

			case 'title':
				return esc_html__( 'Title', 'bdthemes-element-pack' );

			case 'description':
				return esc_html__( 'Description', 'bdthemes-element-pack' );

			case 'slide_button':
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
			case 'sub_title':
                return 'LINE';

			case 'title':
				return 'LINE';

			case 'description':
				return 'AREA';

			case 'slide_button':
				return 'LINE';

			default:
				return '';
		}
	}

}
