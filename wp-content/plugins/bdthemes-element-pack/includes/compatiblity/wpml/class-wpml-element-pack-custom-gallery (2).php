<?php


if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Class WPML_Jet_Elements_Custom_Gallery
 */
class WPML_ElementPack_Custom_Gallery extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'gallery';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array( 'image_title', 'image_text' );
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_title( $field ) {
		switch( $field ) {
			case 'image_title':
				return esc_html__( 'Title', 'bdthemes-element-pack' );

			case 'image_text':
				return esc_html__( 'Content', 'bdthemes-element-pack' );

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
			case 'image_title':
				return 'LINE';

			case 'image_text':
				return 'AREA';

			default:
				return '';
		}
	}

}
