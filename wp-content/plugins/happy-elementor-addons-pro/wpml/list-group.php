<?php
/**
 * List Group
 */
namespace Happy_Addons_Pro;

defined( 'ABSPATH' ) || die();

class WPML_List_Group extends \WPML_Elementor_Module_With_Items {

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
			'description',
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
				return __( 'List Group: Number', 'happy-addons-pro' );
			case 'title':
				return __( 'List Group: Title', 'happy-addons-pro' );
			case 'description':
				return __( 'List Group: Description', 'happy-addons-pro' );
			case 'url':
				return __( 'List Group: Link', 'happy-addons-pro' );
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
			case 'description':
				return 'AREA';
			case 'url':
				return 'LINK';
			default:
				return '';
		}
	}
}
