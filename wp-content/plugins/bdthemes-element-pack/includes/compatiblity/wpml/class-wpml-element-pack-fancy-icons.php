<?php



if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Class WPML_ElementPack_Fancy_Icons
 */
class WPML_ElementPack_Fancy_Icons extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'share_items';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array( 'social_name' );
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_title( $field ) {
		switch( $field ) {

			case 'social_name':
				return esc_html__( 'Social Name', 'bdthemes-element-pack' );

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
			case 'social_name':
				return 'LINE';

			default:
				return '';
		}
	}

}
