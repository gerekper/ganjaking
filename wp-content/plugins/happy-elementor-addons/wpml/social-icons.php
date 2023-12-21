<?php
/**
 * Social Icons integration
 */
namespace Happy_Addons\Elementor;

defined( 'ABSPATH' ) || die();

class WPML_Social_Icons extends \WPML_Elementor_Module_With_Items  {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'ha_social_icon_list';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return [
			'ha_social_icon_title',
			'ha_social_link' => ['url']
		];
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		switch ( $field ) {
			case 'ha_social_icon_title':
				return __( 'Social Icons: Title', 'happy-elementor-addons' );
			case 'url':
				return __( 'Social Icons: Link', 'happy-elementor-addons' );
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
			case 'ha_social_icon_title':
				return 'LINE';
			case 'url':
				return 'LINK';
			default:
				return '';
		}
	}
}
