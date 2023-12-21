<?php
/**
 * Timeline integration
 */
namespace Happy_Addons_Pro;

defined( 'ABSPATH' ) || die();

class WPML_Timeline extends \WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'timeline_item';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return [
			'time_text',
			'title',
			'content',
			'button_text',
			'button_link' => ['url']
		];
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		switch ( $field ) {
			case 'time_text':
				return __( 'Timeline: Text Time', 'happy-addons-pro' );
			case 'title':
				return __( 'Timeline: Title', 'happy-addons-pro' );
			case 'content':
				return __( 'Timeline: Content', 'happy-addons-pro' );
			case 'button_text':
				return __( 'Timeline: Button Text', 'happy-addons-pro' );
			case 'url':
				return __( 'Timeline: Button Link', 'happy-addons-pro' );
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
			case 'time_text':
			case 'title':
			case 'button_text':
				return 'LINE';
			case 'content':
				return 'VISUAL';
			case 'url':
				return 'LINK';
			default:
				return '';
		}
	}
}
