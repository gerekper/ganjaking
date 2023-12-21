<?php
/**
 * Hotspots
 */
namespace Happy_Addons_Pro;

defined( 'ABSPATH' ) || die();

class WPML_Hotspots extends \WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'spots';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return ['text', 'content'];
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		switch ( $field ) {
			case 'text':
				return __( 'Hotspots: Text', 'happy-addons-pro' );
			case 'content':
				return __( 'Hotspots: Content', 'happy-addons-pro' );
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
				return 'LINE';
			case 'content':
				return 'VISUAL';
			default:
				return '';
		}
	}
}
