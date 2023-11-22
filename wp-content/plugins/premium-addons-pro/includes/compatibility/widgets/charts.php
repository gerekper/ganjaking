<?php
/**
 * PA WPML Charts.
 */

namespace PremiumAddonsPro\Compatibility\WPML\Widgets;

use WPML_Elementor_Module_With_Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No access of directly access.
}

/**
 * Charts
 *
 * Registers translatable widget with items.
 *
 * @since 1.4.8
 */
class Charts extends WPML_Elementor_Module_With_Items {

	/**
	 * Retrieve the field name.
	 *
	 * @since 1.4.8
	 * @return string
	 */
	public function get_items_field() {

		return 'y_axis_data';

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
			'y_axis_column_title',
			'y_axis_column_data',
			'y_axis_urls',
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
			case 'y_axis_column_title':
				return esc_html__( 'Charts: Y-axis Data Title', 'premium-addons-pro' );

			case 'y_axis_urls':
				return esc_html__( 'Charts: Y-axis Data URLs', 'premium-addons-pro' );

			case 'y_axis_column_data':
				return esc_html__( 'Charts: Y-axis Data', 'premium-addons-pro' );

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

		return 'LINE';
	}

}
