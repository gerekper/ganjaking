<?php
/**
 * PA WPML Image Hotspots.
 */

namespace PremiumAddonsPro\Compatibility\WPML\Widgets;

use WPML_Elementor_Module_With_Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No access of directly access.
}

/**
 * Hotspots
 *
 * Registers translatable widget with items.
 *
 * @since 1.4.8
 */
class Hotspots extends WPML_Elementor_Module_With_Items {

	/**
	 * Retrieve the field name.
	 *
	 * @since 1.4.8
	 * @return string
	 */
	public function get_items_field() {
		return 'premium_image_hotspots_icons';
	}

	/**
	 * Retrieve the fields inside the repeater.
	 *
	 * @since 1.4.8
	 *
	 * @return array
	 */
	public function get_fields() {
		return array(
			'premium_image_hotspots_text',
			'premium_image_hotspots_tooltips_texts',
			'premium_image_hotspots_url' => array( 'url' ),
			'premium_image_hotspots_link_text',
			'premium_image_hotspots_tooltips_temp',
		);
	}

	/**
	 * Get the title for each repeater string
	 *
	 * @since 1.4.8
	 *
	 * @param string $field Control ID.
	 *
	 * @return string
	 */
	protected function get_title( $field ) {

		switch ( $field ) {
			case 'premium_image_hotspots_tooltips_texts':
				return esc_html__( 'Hotspots: Content', 'premium-addons-pro' );

			case 'premium_image_hotspots_tooltips_temp':
				return esc_html__( 'Hotspots: Content Template ID', 'premium-addons-pro' );

			case 'premium_image_hotspots_link_text':
				return esc_html__( 'Hotspots: Link Title', 'premium-addons-pro' );

			case 'premium_image_hotspots_text':
				return esc_html__( 'Hotspots: Hotspot Text', 'premium-addons-pro' );

			case 'url':
				return esc_html__( 'Hotspots: Hotspot Link', 'premium-addons-pro' );

			default:
				return '';
		}

	}

	/**
	 * Get `editor_type` for each repeater string
	 *
	 * @since 1.4.8
	 *
	 * @param string $field Control ID.
	 *
	 * @return string
	 */
	protected function get_editor_type( $field ) {

		switch ( $field ) {
			case 'premium_image_hotspots_tooltips_texts':
				return 'AREA';

			case 'url':
				return 'LINK';

			case 'premium_image_hotspots_link_text':
			case 'premium_image_hotspots_text':
			case 'premium_image_hotspots_tooltips_temp':
				return 'LINE';

			default:
				return '';
		}

	}

}
