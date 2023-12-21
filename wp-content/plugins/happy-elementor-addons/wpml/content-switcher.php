<?php
/**
 * Content Switcher integration
 */
namespace Happy_Addons\Elementor;

defined( 'ABSPATH' ) || die();

class WPML_Content_Switcher extends \WPML_Elementor_Module_With_Items  {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'content_list';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return [
			'title',
			'plain_content'
		];
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		switch ( $field ) {
			case 'title':
				return __( 'Content Switcher: Title', 'happy-elementor-addons' );
			case 'plain_content':
				return __( 'Content Switcher: Plain/ HTML Text', 'happy-elementor-addons' );
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
			case 'plain_content':
				return 'AREA';
			default:
				return '';
		}
	}
}
