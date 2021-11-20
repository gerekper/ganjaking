<?php
/**
 * WooCommerce Order Status Manager
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Order Status Manager to newer
 * versions in the future. If you wish to customize WooCommerce Order Status Manager for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-order-status-manager/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Get order status posts
 *
 * @since 1.5.0
 * @param array $args Optional. List of get_post args
 * @return \WP_Post[] Array of WP_Post objects
 */
function wc_order_status_manager_get_order_status_posts( $args = array() ) {

	$defaults = array(
		'post_type'        => 'wc_order_status',
		'post_status'      => 'publish',
		'posts_per_page'   => -1,
		'suppress_filters' => false,
		'orderby'          => 'menu_order',
		'order'            => 'ASC',
	);

	$posts = wp_cache_get( 'wc_order_status_manager_order_status_posts' );

	if ( ! $posts ) {

		$posts = get_posts( wp_parse_args( $args, $defaults ) );

		// expire cache after 1 second to avoid potential issues with persistent caching
		wp_cache_set( 'wc_order_status_manager_order_status_posts', $posts, null, 1 );
	}

	return $posts;
}
