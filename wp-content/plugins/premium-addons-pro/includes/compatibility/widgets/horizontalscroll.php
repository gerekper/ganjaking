<?php
/**
 * PA WPML Horizontal Scroll.
 */

namespace PremiumAddonsPro\Compatibility\WPML\Widgets;

use WPML_Elementor_Module_With_Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No access of directly access.
}

/**
 * Horizontal Scroll
 *
 * Registers translatable widget with items.
 *
 * @since 2.2.5
 */
class HorizontalScroll extends WPML_Elementor_Module_With_Items {

	/**
	 * Retrieve the field name.
	 *
	 * @since 2.2.5
	 * @return string
	 */
	public function get_items_field() {
		return 'section_repeater';
	}

	/**
	 * Retrieve the fields inside the repeater.
	 *
	 * @since 2.2.5
	 *
	 * @return array
	 */
	public function get_fields() {
		return array(
			'section_id',
			'anchor_id',
			'section_template',
		);
	}

	/**
	 * Get the title for each repeater string
	 *
	 * @since 2.2.5
	 *
	 * @param string $field Control ID.
	 *
	 * @return string field label.
	 */
	protected function get_title( $field ) {

		if ( 'section_id' === $field ) {
			return __( 'Horizontal Scroll: Section ID', 'premium-addons-pro' );
		}

		if ( 'section_template' === $field ) {
			return __( 'Horizontal Scroll: Template ID', 'premium-addons-pro' );
		}

		if ( 'anchor_id' === $field ) {
			return __( 'Horizontal Scroll: Anchor ID', 'premium-addons-pro' );
		}

		return '';

	}

	/**
	 * Get `editor_type` for each repeater string
	 *
	 * @since 2.2.5
	 *
	 * @param string $field Control ID.
	 *
	 * @return string field type.
	 */
	protected function get_editor_type( $field ) {

		return 'LINE';
	}

}
