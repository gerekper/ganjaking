<?php
/**
 * Feature List
 */
namespace Happy_Addons_Pro;

defined( 'ABSPATH' ) || die();

class WPML_Feature_List extends \WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'list_item';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return [
			'number',
			'title',
			'link' => ['url']
		];
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		switch ( $field ) {
			case 'number':
				return __( 'Feature List: Number', 'happy-addons-pro' );
			case 'title':
				return __( 'Feature List: Title', 'happy-addons-pro' );
			case 'url':
				return __( 'Feature List: Link', 'happy-addons-pro' );
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
			case 'number':
			case 'title':
				return 'LINE';
			case 'url':
				return 'LINK';
			default:
				return '';
		}
	}
}
