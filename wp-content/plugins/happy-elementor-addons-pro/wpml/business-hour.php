<?php
/**
 * Business Hour integration
 */
namespace Happy_Addons_Pro;

defined( 'ABSPATH' ) || die();

class WPML_Business_Hour extends \WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'business_hour_list';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return ['day','time'];
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		switch ( $field ) {
			case 'day':
				return __( 'Business Hour: Day', 'happy-addons-pro' );
			case 'time':
				return __( 'Business Hour: Time', 'happy-addons-pro' );
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
			case 'day':
			case 'time':
				return 'LINE';
			default:
				return '';
		}
	}
}
