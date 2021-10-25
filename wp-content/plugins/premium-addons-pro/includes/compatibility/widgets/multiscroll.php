<?php
/**
 * PA WPML Multiscroll.
 */

namespace PremiumAddonsPro\Compatibility\WPML\Widgets;

use WPML_Elementor_Module_With_Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No access of directly access.
}

/**
 * Multi Scroll
 *
 * Registers translatable widget with items.
 *
 * @since 1.4.8
 */
class MultiScroll extends WPML_Elementor_Module_With_Items {

	/**
	 * Retrieve the field name.
	 *
	 * @since 1.4.8
	 * @return string
	 */
	public function get_items_field() {
		return 'left_side_repeater';
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
			'left_side_template',
			'right_side_template',
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

		if ( 'left_side_template' === $field ) {
			return __( 'Multi Scroll: Left Template ID', 'premium-addons-pro' );
		}

		if ( 'right_side_template' === $field ) {
			return __( 'Multi Scroll: Right Template ID', 'premium-addons-pro' );
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

		return 'LINE';
	}

}
