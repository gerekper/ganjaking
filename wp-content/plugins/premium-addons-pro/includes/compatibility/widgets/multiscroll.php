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
			'left_side_text',
			'right_side_text',
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
			case 'left_side_template':
				return esc_html__( 'Multi Scroll: Left Template ID', 'premium-addons-pro' );

			case 'right_side_template':
				return esc_html__( 'Multi Scroll: Right Template ID', 'premium-addons-pro' );

			case 'left_side_text':
				return esc_html__( 'Multi Scroll: Left Side Text', 'premium-addons-pro' );

			case 'right_side_text':
				return esc_html__( 'Multi Scroll: Right Side Text', 'premium-addons-pro' );

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
			case 'left_side_template':
			case 'right_side_template':
				return 'LINE';

			case 'left_side_text':
			case 'right_side_text':
				return 'VISUAL';

			default:
				return '';
		}
	}

}
