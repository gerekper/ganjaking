<?php
/**
 * Pie Chart
 */
namespace Happy_Addons_Pro;

defined( 'ABSPATH' ) || die();

class WPML_Pie_Chart extends \WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'chart_data';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return ['label', 'data'];
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		switch ( $field ) {
			case 'label':
				return __( 'Pie Chart: Label', 'happy-addons-pro' );
			case 'data':
				return __( 'Pie Chart: Data', 'happy-addons-pro' );
			default:
				return '';
		}
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'label':
			case 'data':
			return 'LINE';
			default:
				return '';
		}
	}
}
