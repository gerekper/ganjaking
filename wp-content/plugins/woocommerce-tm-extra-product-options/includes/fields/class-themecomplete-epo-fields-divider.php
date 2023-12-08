<?php
/**
 * Divider Field class
 *
 * @package Extra Product Options/Fields
 * @version 6.0
 * phpcs:disable PEAR.NamingConventions.ValidClassName
 */

defined( 'ABSPATH' ) || exit;

/**
 * Divider Field class
 *
 * @package Extra Product Options/Fields
 * @version 6.0
 */
class THEMECOMPLETE_EPO_FIELDS_divider extends THEMECOMPLETE_EPO_FIELDS {

	/**
	 * Display field array
	 *
	 * @param array<mixed> $element The element array.
	 * @param array<mixed> $args Array of arguments.
	 * @return array<mixed>
	 * @since 1.0
	 */
	public function display_field( $element = [], $args = [] ) {

		return apply_filters( 'wc_epo_display_field_variations', [], $this, $element, $args );
	}

	/**
	 * Field validation
	 *
	 * @return array<mixed>
	 * @since 1.0
	 */
	public function validate() {
		return [
			'passed'  => true,
			'message' => false,
		];
	}
}
