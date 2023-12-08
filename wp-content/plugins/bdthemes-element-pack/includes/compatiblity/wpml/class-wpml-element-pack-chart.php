<?php


if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Class WPML_Jet_Elements_Chart
 */
class WPML_ElementPack_Chart extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'datasets';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array( 'label', 'data' );
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_title( $field ) {
		switch( $field ) {
			case 'label':
				return esc_html__( 'Label', 'bdthemes-element-pack' );

			case 'data':
				return esc_html__( 'Data', 'bdthemes-element-pack' );

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
			case 'label':
				return 'LINE';

			case 'data':
				return 'LINE';

			default:
				return '';
		}
	}

}
