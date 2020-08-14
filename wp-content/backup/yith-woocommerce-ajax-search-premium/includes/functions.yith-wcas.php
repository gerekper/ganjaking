<?php
/**
 * Functions
 *
 * @author YITH
 * @package YITH WooCommerce Ajax Search Premium
 * @version 1.2
 */

if ( ! defined( 'YITH_WCAS' ) ) {
	exit; } // Exit if accessed directly

if ( ! function_exists( 'yit_get_shop_categories' ) ) {
	/**
	 * Get the categories of shop.
	 *
	 * @param   bool $show_all Flag.
	 *
	 * @return int|WP_Error|WP_Term[]
	 */
	function yith_wcas_get_shop_categories( $show_all = true ) {

		$args = apply_filters(
			'yith_wcas_form_cat_args',
			array(
				'hide_empty' => true,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);

		if ( ! $show_all ) {
			$args = array_merge(
				$args,
				array(
					'parent'       => 0,
					'hierarchical' => 0,
				)
			);
		}

		$terms = get_terms( 'product_cat', apply_filters( 'yith_wcas_form_cat_args', $args ) );

		return $terms;
	}
}

/**
 * Get microtime.
 *
 * @return float
 */
function getmicrotime() {
	list($usec, $sec) = explode( ' ', microtime() );
	return ( (float) $usec + (float) $sec );
}

add_action( 'admin_init', 'yith_ajax_search_update_1_6_9' );
/**
 * Update script.
 */
function yith_ajax_search_update_1_6_9() {
	$ywcas_option_version = get_option( 'yith_wcas_option_version', '1.0.0' );
	if ( $ywcas_option_version && version_compare( $ywcas_option_version, '1.6.9', '<' ) ) {
		$sale_badge_bgcolor       = get_option( 'yith_wcas_sale_badge_bgcolor' );
		$outofstock_badge_bgcolor = get_option( 'yith_wcas_outofstock_badge_bgcolor' );
		$featured_badge_bgcolor   = get_option( 'yith_wcas_featured_badge_bgcolor' );

		if ( $sale_badge_bgcolor ) {
			update_option(
				'yith_wcas_sale_badge',
				array(
					'bgcolor' => $sale_badge_bgcolor,
					'color'   => get_option( 'yith_wcas_sale_badge_color' ),
				)
			);
			delete_option( 'yith_wcas_sale_badge_bgcolor' );
			delete_option( 'yith_wcas_sale_badge_color' );
		}

		if ( $outofstock_badge_bgcolor ) {
			update_option(
				'yith_wcas_outofstock',
				array(
					'bgcolor' => $outofstock_badge_bgcolor,
					'color'   => get_option( 'yith_wcas_outofstock_badge_color' ),
				)
			);
			delete_option( 'yith_wcas_outofstock_badge_bgcolor' );
			delete_option( 'yith_wcas_outofstock_badge_color' );
		}

		if ( $featured_badge_bgcolor ) {
			update_option(
				'yith_wcas_featured_badge',
				array(
					'bgcolor' => $featured_badge_bgcolor,
					'color'   => get_option( 'yith_wcas_featured_badge_color' ),
				)
			);
			delete_option( 'yith_wcas_featured_badge_bgcolor' );
			delete_option( 'yith_wcas_featured_badge_color' );
		}
	}

	update_option( 'yith_wcas_option_version', '1.6.9' );
}
