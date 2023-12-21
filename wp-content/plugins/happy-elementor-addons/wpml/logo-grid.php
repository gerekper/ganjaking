<?php
/**
 * Logo Grid integration
 */
namespace Happy_Addons\Elementor;

defined( 'ABSPATH' ) || die();

class WPML_Logo_Grid extends \WPML_Elementor_Module_With_Items  {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'logo_list';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return [
			'name',
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
			case 'name':
				return __( 'Logo Grid: Brand Name', 'happy-elementor-addons' );
			case 'url':
				return __( 'Logo Grid: Link', 'happy-elementor-addons' );
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
			case 'name':
				return 'LINE';
			case 'url':
				return 'LINK';
			default:
				return '';
		}
	}
}
