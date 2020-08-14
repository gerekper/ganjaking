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
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'yfwp_get_option' ) ) {

	/**
	 * Get plugin option
	 *
	 * @param   $option  string
	 * @param   $default mixed
	 *
	 * @return  mixed
	 * @since   1.0.0
	 *
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function yfwp_get_option( $option, $default = false ) {
		return YITH_FAQ_Settings::get_instance()->get_option( 'faq', $option, $default );
	}
}

if ( ! function_exists( 'yfwp_get_categories' ) ) {

	/**
	 * Get FAQ Categories
	 *
	 * @return  array
	 * @since   1.1.5
	 *
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function yfwp_get_categories() {
		$categories = get_terms(
			array(
				'taxonomy'   => YITH_FWP()->taxonomy,
				'hide_empty' => false,
			)
		);

		$terms = array();
		if ( $categories ) {
			foreach ( $categories as $category ) {
				$terms[ $category->term_id ] = $category->name;
			}
		}

		return $terms;
	}
}

$deprecated_filters_map = array(
	'yfwp_instantiate_shortcode_button' => array(
		'since'  => '1.1.5',
		'use'    => 'yith_faq_instantiate_shortcode_button',
		'params' => 1,
	),
	'yith_fwp_rewrite'                  => array(
		'since'  => '1.1.5',
		'use'    => 'yith_faq_rewrite',
		'params' => 1,
	),
	'yith_fwp_needs_flushing'           => array(
		'since'  => '1.1.5',
		'use'    => 'yith_faq_needs_flushing',
		'params' => 1,
	),
	'yfwp_add_scripts'                  => array(
		'since'  => '1.1.5',
		'use'    => 'yith_faq_add_scripts',
		'params' => 2,
	),
	'yith_fwp_search_placeholder'       => array(
		'since'  => '1.1.5',
		'use'    => 'yith_faq_search_placeholder',
		'params' => 1,
	),

);

foreach ( $deprecated_filters_map as $deprecated_filter => $options ) {
	$new_filter = $options['use'];
	$params     = $options['params'];
	$since      = $options['since'];
	add_filter(
		$new_filter,
		function () use ( $deprecated_filter, $since, $new_filter ) {
			$args = func_get_args();
			$r    = $args[0];

			if ( has_filter( $deprecated_filter ) ) {
				error_log( sprintf( 'Deprecated filter: %s since %s. Use %s instead!', $deprecated_filter, $since, $new_filter ) );

				$r = call_user_func_array( 'apply_filters', array_merge( array( $deprecated_filter ), $args ) );
			}

			return $r;
		},
		10,
		$params
	);
}
