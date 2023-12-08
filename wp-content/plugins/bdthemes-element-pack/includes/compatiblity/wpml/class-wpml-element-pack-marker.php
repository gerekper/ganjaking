<?php


if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Class WPML_Jet_Elements_Marker
 */
class WPML_ElementPack_Marker extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'markers';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array( 'marker_title', 'text' );
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_title( $field ) {
		switch( $field ) {
			case 'marker_title':
				return esc_html__( 'Tooltip Text', 'bdthemes-element-pack' );

			case 'text':
				return esc_html__( 'Marker Text', 'bdthemes-element-pack' );

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
			case 'marker_title':
				return 'AREA';

			case 'text':
				return 'LINE';

			default:
				return '';
		}
	}

}
