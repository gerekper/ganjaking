<?php
/**
 * product-search.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 1.0.1
 */

ob_start();


define( 'DOING_AJAX', true );

if ( !defined( 'ABSPATH' ) ) {
	$wp_load = 'wp-load.php';
	$max_depth = 100; 
	while ( !file_exists( $wp_load ) && ( $max_depth > 0 ) ) {
		$wp_load = '../' . $wp_load;
		$max_depth--;
	}
	if ( file_exists( $wp_load ) ) {
		require_once $wp_load;
	}
}

if ( defined( 'ABSPATH' ) ) {
	$results = WooCommerce_Product_Search_Service::request_results();
	$ob = ob_get_clean();
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG && $ob ) {
		error_log( $ob );
	}
	echo json_encode( $results );
	exit;
} else {
	$ob = ob_get_clean();
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG && $ob ) {
		error_log( $ob );
	}
	echo json_encode( array( '' ) );
	exit;
}
