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
			'premium_image_hotspots_tooltips_texts',
			'premium_image_hotspots_link_text',
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

		if ( 'premium_image_hotspots_tooltips_texts' === $field ) {
			return __( 'Hotspots: Content', 'premium-addons-pro' );
		}
		if ( 'premium_image_hotspots_link_text' === $field ) {
			return __( 'Hotspots: Link Title', 'premium-addons-pro' );
		}

		return '';

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

		if ( 'premium_image_hotspots_tooltips_texts' === $field ) {
			return 'AREA';
		}
		if ( 'premium_image_hotspots_link_text' === $field ) {
			return 'LINE';
		}

		return '';
	}

}
