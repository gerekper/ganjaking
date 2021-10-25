<?php
/**
 * PA WPML Tabs.
 */

namespace PremiumAddonsPro\Compatibility\WPML\Widgets;

use WPML_Elementor_Module_With_Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No access of directly access.
}

/**
 * Fancy Text
 *
 * Registers translatable widget with items.
 *
 * @since 1.4.8
 */
class Tabs extends WPML_Elementor_Module_With_Items {

	/**
	 * Retrieve the field name.
	 *
	 * @since 1.4.8
	 * @return string
	 */
	public function get_items_field() {
		return 'premium_tabs_repeater';
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
			'premium_tabs_content_temp',
			'premium_tabs_content_text',
			'premium_tabs_title',
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

		if ( 'premium_tabs_content_temp' === $field ) {
			return __( 'Tabs: Tab Template ID', 'premium-addons-pro' );
		}
		if ( 'premium_tabs_content_text' === $field ) {
			return __( 'Tabs: Tab Content', 'premium-addons-pro' );
		}
		if ( 'premium_tabs_title' === $field ) {
			return __( 'Tabs: Tab Title', 'premium-addons-pro' );
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

		if ( 'premium_tabs_content_temp' === $field || 'premium_tabs_title' === $field ) {
			return 'LINE';
		}
		if ( 'premium_tabs_content_text' === $field ) {
			return 'AREA';
		}

		return '';
	}

}
