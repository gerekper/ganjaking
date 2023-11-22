<?php


if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Class WPML_ElementPack_Honeycombs
 */
class WPML_ElementPack_Honeycombs extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'honeycombs_list';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array( 'honeycombs_title', 'honeycombs_content' );
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_title( $field ) {
		switch( $field ) {

			case 'honeycombs_title':
				return esc_html__( 'Title', 'bdthemes-element-pack' );

			case 'honeycombs_content':
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
			case 'honeycombs_title':
                return 'LINE';

			case 'honeycombs_content':
				return 'AREA';

			default:
				return '';
		}
	}

}
