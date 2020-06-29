<?php
/**
 * DB update functions
 *
 * Functions used to upgrade to specific versions of Mix and Match.
 *
 * @author   SomewhereWarm
 * @category Core
 * @package  WooCommerce Mix and Match Products/Update
 * @since    1.2.0
 * @version  1.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Data Update for Version 1.2.0.
 *
 * @param obj $updater The background updater class object.
 * @return void
 */
function wc_mnm_update_120_main( $updater ) {

	global $wpdb;

	$mnm_term = get_term_by( 'slug', 'mix-and-match', 'product_type' );

	if ( $mnm_term ) {

		$mnms = $wpdb->get_results( $wpdb->prepare( "
			SELECT DISTINCT posts.ID AS mnm_id FROM {$wpdb->posts} AS posts
			LEFT JOIN {$wpdb->term_relationships} AS rel ON ( posts.ID = rel.object_id )
			LEFT JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id AND postmeta.meta_key = '_mnm_base_price'
			WHERE rel.term_taxonomy_id = %d
			AND posts.post_type = 'product'
			AND postmeta.meta_value IS NULL
		", $mnm_term->term_taxonomy_id ) );

		if ( ! empty( $mnms ) ) {
			foreach ( $mnms as $index => $mnm ) {

				// Make sure we are nowhere close to memory & PHP timeout limits - check state every 20 migrated products.
				if ( $index % 20 === 19 ) {
					if ( $updater->time_exceeded() || $updater->memory_exceeded() ) {
						return -1;
					}
				}

				$mnm_id              = (int) $mnm->mnm_id;
				$priced_individually = get_post_meta( $mnm_id, '_mnm_per_product_pricing', true );

				// Convert prices to MnM's "base" prices
				if ( 'yes' === $priced_individually ) {
					$price         = get_post_meta( $mnm_id, '_base_price', true );
					$regular_price = get_post_meta( $mnm_id, '_base_regular_price', true );
					$sale_price    = get_post_meta( $mnm_id, '_base_sale_price', true );
				} else {
					$price         = get_post_meta( $mnm_id, '_price', true );
					$regular_price = get_post_meta( $mnm_id, '_regular_price', true );
					$sale_price    = get_post_meta( $mnm_id, '_sale_price', true );
				}

				update_post_meta( $mnm_id, '_mnm_base_price', $price );
				update_post_meta( $mnm_id, '_mnm_base_regular_price', $regular_price );
				update_post_meta( $mnm_id, '_mnm_base_sale_price', $sale_price );

				delete_post_meta( $mnm_id, '_base_price' );
				delete_post_meta( $mnm_id, '_base_regular_price' );
				delete_post_meta( $mnm_id, '_base_sale_price' );

				// Convert container size to support min/max sizes.
				$min_container_size = get_post_meta( $mnm_id, '_mnm_container_size', true );
				$max_container_size = get_post_meta( $mnm_id, '_mnm_max_container_size', true );

				// If a max container size exists, Min/Max plugin was used
				if ( in_array( '_mnm_max_container_size', get_post_custom_keys( $mnm_id ) ) ) {

					// Unlimited.
					if ( $min_container_size <= 1 && $max_container_size == 0 ) {
						$min_container_size = 1;
						$max_container_size = '';
					} else {
						$min_container_size = max( $min_container_size, 1 );
						$max_container_size = $max_container_size ? $max_container_size : '';
					}

					// Default MNM plugin.
				} else {

					// Unlimited.
					if ( $min_container_size == 0 ) {
						$min_container_size = 1;
						$max_container_size = '';
						// Fixed Container.
					} else {
						$min_container_size = $min_container_size;
						$max_container_size = $min_container_size;
					}
				}

				update_post_meta( $mnm_id, '_mnm_min_container_size', $min_container_size );
				update_post_meta( $mnm_id, '_mnm_max_container_size', $max_container_size );

				delete_post_meta( $mnm_id, '_mnm_container_size' );
			}
		}
	}
}
