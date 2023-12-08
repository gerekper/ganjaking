<?php

use \Elementor\Control_Select2;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor emoji one area control.
 *
 * A control for displaying a textarea with the ability to add emojis.
 *
 * @since 2.0.0
 */
class Theplus_Query extends Control_Select2 {

	/**
	 * Get control type.
	 *
	 * Retrieve the control type, in this case `plus-query`.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'plus-query';
	}
	
	
}