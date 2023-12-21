<?php
/**
 * Pricing Table integration
 */
namespace Happy_Addons_Pro;

defined( 'ABSPATH' ) || die();

class WPML_Pricing_Table extends \WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'features_list';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return ['text','tooltip_text'];
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		switch ( $field ) {
			case 'text':
				return __( 'Pricing Table: Text', 'happy-addons-pro' );
			case 'tooltip_text':
				return __( 'Pricing Table: Tooltip Text', 'happy-addons-pro' );
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
			case 'text':
			case 'tooltip_text':
				return 'AREA';
			default:
				return '';
		}
	}
}
