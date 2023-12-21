<?php
/**
 * Price Menu integration
 */
namespace Happy_Addons_Pro;

defined( 'ABSPATH' ) || die();

class WPML_Price_Menu extends \WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'price_list';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return [
			'title',
			'badge_icon_title',
			'badge_text',
			'desc',
			'price',
			'old_price',
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
			case 'title':
				return __( 'Price Menu: Title', 'happy-addons-pro' );
			case 'badge_icon_title':
				return __( 'Price Menu: Icon Hover Title', 'happy-addons-pro' );
			case 'badge_text':
				return __( 'Price Menu: Badge Text', 'happy-addons-pro' );
			case 'desc':
				return __( 'Price Menu: Description', 'happy-addons-pro' );
			case 'price':
				return __( 'Price Menu: Price', 'happy-addons-pro' );
			case 'old_price':
				return __( 'Price Menu: Old Price', 'happy-addons-pro' );
			case 'url':
				return __( 'Price Menu: Link', 'happy-addons-pro' );
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
			case 'badge_icon_title':
			case 'badge_text':
			case 'price':
			case 'old_price':
				return 'LINE';
			case 'desc':
				return 'AREA';
			case 'url':
				return 'LINK';
			default:
				return '';
		}
	}
}
