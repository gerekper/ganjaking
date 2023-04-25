<?php
/**
 * Admin Init
 *
 * @package WC_OD/Admin
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;


/** Shop Orders functions *****************************************************/


/**
 * Adds the query vars for order by our custom columns.
 *
 * @since 1.0.0
 *
 * @global string $typenow The current post type.
 *
 * @param array $vars The query vars.
 * @return array The filtered query vars.
 */
function wc_od_admin_shop_order_orderby( $vars ) {
	global $typenow;

	if ( 'shop_order' !== $typenow ) {
		return $vars;
	}

	// Sorting
	if ( isset( $vars['orderby'] ) ) {
		if ( in_array( $vars['orderby'], array( 'shipping_date', 'delivery_date' ) ) ) {
			$vars = array_merge(
				$vars,
				array(
					'meta_key' => "_{$vars['orderby']}",
					'orderby'  => 'meta_value_num',
				)
			);
		}
	}

	return $vars;
}
add_filter( 'request', 'wc_od_admin_shop_order_orderby' );

/**
 * Filters the order by query for cast the meta_value as date.
 *
 * @since 1.0.0
 *
 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
 *
 * @param string $orderby The orderby query.
 * @param array  $query   The query parameters.
 * @return string The filtered orderby query.
 */
function wc_od_admin_posts_orderby_date( $orderby, $query ) {
	global $wpdb;

	if ( 'shop_order' === $query->get( 'post_type' ) && in_array( $query->get( 'meta_key' ), array( '_shipping_date', '_delivery_date' ) ) ) {
		$orderby = "CAST( $wpdb->postmeta.meta_value AS DATE ) " . $query->get( 'order' );
	}

	return $orderby;
}
add_filter( 'posts_orderby', 'wc_od_admin_posts_orderby_date', 10, 2 );
