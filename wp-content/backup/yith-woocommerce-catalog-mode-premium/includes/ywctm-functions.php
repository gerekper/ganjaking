<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'ywctm_get_theme_name' ) ) {

	/**
	 * Get the current theme name
	 *
	 * @return  string
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_theme_name() {
		$wp_theme = wp_get_theme();

		return is_child_theme() ? $wp_theme->get_template() : strtolower( $wp_theme->get( 'Name' ) );
	}
}

/**
 * WPML RELATED FUNCTIONS
 */
if ( ! function_exists( 'ywctm_is_wpml_active' ) ) {

	/**
	 * Check if WPML is active
	 *
	 * @return  boolean
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_is_wpml_active() {
		global $sitepress;

		return ! empty( $sitepress ) ? true : false;
	}
}
