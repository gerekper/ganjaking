<?php



if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Class WPML_ElementPack_Fancy_List
 */
class WPML_ElementPack_Fancy_List extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'icon_list';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array( 'text', 'text_details' );
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_title( $field ) {
		switch( $field ) {

			case 'text':
				return esc_html__( 'Title', 'bdthemes-element-pack' );

			case 'text_details':
				return esc_html__( 'Sub Title', 'bdthemes-element-pack' );

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
			case 'text':
                return 'LINE';

			case 'text_details':
				return 'LINE';

			default:
				return '';
		}
	}

}
