<?php
/**
 * PA WPML Tables.
 */

namespace PremiumAddonsPro\Compatibility\WPML\Widgets;

use WPML_Elementor_Module_With_Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No access of directly access.
}

/**
 * TableBody
 *
 * Registers translatable widget with items.
 *
 * @since 2.1.4
 */
class Table extends WPML_Elementor_Module_With_Items {

	/**
	 * Retrieve the field name.
	 *
	 * @since 2.1.4
	 * @return string
	 */
	public function get_items_field() {
		return 'premium_table_body_repeater';

	}

	/**
	 * Retrieve the fields inside the repeater.
	 *
	 * @since 2.1.4
	 *
	 * @return array
	 */
	public function get_fields() {
		return array(
			'premium_table_text',
			'premium_table_link' => array( 'url' ),
		);
	}

	/**
	 * Get the title for each repeater string
	 *
	 * @since 2.1.4
	 *
	 * @param string $field Control ID.
	 *
	 * @return string
	 */
	protected function get_title( $field ) {

		if ( 'premium_table_text' === $field ) {
			return __( 'Table: Cell Text', 'premium-addons-pro' );
		}
		if ( 'url' === $field ) {
			return __( 'Table: Cell Link', 'premium-addons-pro' );
		}

		return '';

	}

	/**
	 * Get `editor_type` for each repeater string
	 *
	 * @since 2.1.4
	 *
	 * @param string $field Control ID.
	 *
	 * @return string
	 */
	protected function get_editor_type( $field ) {

		switch ( $field ) {
			case 'premium_table_text':
				return 'LINE';
			case 'url':
				return 'LINK';
			default:
				return '';
		}

	}

}
