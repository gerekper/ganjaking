<?php


if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Class WPML_Jet_Elements_IconNav
 */
class WPML_ElementPack_IconNav extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'iconnavs';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array( 'iconnav_title' );
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_title( $field ) {
		switch( $field ) {
			case 'iconnav_title':
				return esc_html__( 'Iconnav Title', 'bdthemes-element-pack' );

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
			case 'iconnav_title':
				return 'LINE';

			default:
				return '';
		}
	}

}
