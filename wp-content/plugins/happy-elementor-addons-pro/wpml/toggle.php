<?php
/**
 * Advanced Toggle integration
 */
namespace Happy_Addons_Pro;

defined( 'ABSPATH' ) || die();

class WPML_Advanced_Toggle extends \WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'tabs';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return ['title','editor'];
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		switch ( $field ) {
			case 'title':
				return __( 'Advanced Toggle: Title', 'happy-addons-pro' );
			case 'editor':
				return __( 'Advanced Toggle: Content Editor', 'happy-addons-pro' );
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
			case 'title':
				return 'LINE';
			case 'editor':
				return 'VISUAL';
			default:
				return '';
		}
	}
}
