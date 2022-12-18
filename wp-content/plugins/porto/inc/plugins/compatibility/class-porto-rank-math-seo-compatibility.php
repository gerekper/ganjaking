<?php
/**
 * Yoast SEO Compatibility class
 *
 * @since 6.5.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Porto_Rank_Math_SEO_Compatibility {
	/**
	 * Constructor
	 */
	public function __construct() {
		if ( ! defined( 'RANK_MATH_VERSION' ) ) {
			return;
		}

		add_filter( 'cmb2_types_esc_text', array( $this, 'cmb2_types_esc_text_filter' ), 4, 99 );
	}

	/**
	 * Fix Setup Wizard Issue
	 */
	public function cmb2_types_esc_text_filter( $escaped_value, $meta_value, $args, $cmb2_field ) {
		if ( ! is_string( $meta_value ) ) {
            return false;
        }

        return NULL;
	}
}

new Porto_Rank_Math_SEO_Compatibility();