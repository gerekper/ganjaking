<?php
/**
 * Accordion
 */
namespace Happy_Addons_Pro;

defined( 'ABSPATH' ) || die();

class WPML_Accordion extends \WPML_Elementor_Module_With_Items {

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
		return ['title', 'editor'];
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		switch ( $field ) {
			case 'title':
				return __( 'Accordion: Title', 'happy-addons-pro' );
			case 'editor':
				return __( 'Accordion: Editor', 'happy-addons-pro' );
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
