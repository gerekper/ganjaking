<?php
/**
 * Composite Products DB update functions
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    3.7.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wc_cp_update_370_delete_unused_meta() {

	global $wpdb;

	// Delete unused meta.
	$wpdb->query( "
		DELETE FROM {$wpdb->postmeta}
		WHERE ( meta_key = '_min_composite_price'
		OR meta_key = '_max_composite_price' )
	" );
}

function wc_cp_update_370_main( $updater = false ) {

	global $wpdb;

	// Grab post IDs to update.
	$composites = $wpdb->get_results( "
		SELECT DISTINCT posts.ID AS composite_id FROM {$wpdb->posts} AS posts
		LEFT OUTER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id AND postmeta.meta_key = '_bto_data'
		LEFT OUTER JOIN {$wpdb->postmeta} AS postmeta2 ON posts.ID = postmeta2.post_id AND postmeta2.meta_key = '_per_product_shipping_bto'
		WHERE posts.post_type = 'product'
		AND postmeta.meta_value IS NOT NULL
		AND postmeta2.meta_value IS NOT NULL
	" );

	if ( ! empty( $composites ) ) {
		foreach ( $composites as $index => $composite ) {

			// Make sure we are nowhere close to memory & PHP timeout limits - check state every 20 migrated products.
			if ( is_object( $updater ) ) {
				if ( $index % 20 === 19 ) {
					if ( $updater->time_exceeded() || $updater->memory_exceeded() ) {
						return -1;
					}
				}
			}

			$composite_id         = (int) $composite->composite_id;
			$composite_data       = get_post_meta( $composite_id, '_bto_data', true );
			$priced_individually  = get_post_meta( $composite_id, '_per_product_pricing_bto', true );
			$shipped_individually = get_post_meta( $composite_id, '_per_product_shipping_bto', true );

			if ( ! empty( $composite_data ) ) {

				// Get product type.
				$product_type = WC_Product_Factory::get_product_type( $composite_id );

				// Copy new data into serialized array.
				foreach ( $composite_data as $component_id => $component_data ) {

					$composite_data[ $component_id ][ 'priced_individually' ]  = 'yes' === $priced_individually ? 'yes' : 'no';
					$composite_data[ $component_id ][ 'shipped_individually' ] = 'yes' === $shipped_individually ? 'yes' : 'no';
				}

				// Save new data array.
				update_post_meta( $composite_id, '_bto_data', $composite_data );

				// Copy base price to price fields, if applicable.
				if ( 'yes' === $priced_individually && 'composite' === $product_type ) {

					delete_post_meta( $composite_id, '_price' );
					delete_post_meta( $composite_id, '_regular_price' );
					delete_post_meta( $composite_id, '_sale_price' );

					$base_price         = get_post_meta( $composite_id, '_base_price', true );
					$base_regular_price = get_post_meta( $composite_id, '_base_regular_price', true );
					$base_sale_price    = get_post_meta( $composite_id, '_base_sale_price', true );

					update_post_meta( $composite_id, '_price', $base_price );
					update_post_meta( $composite_id, '_regular_price', $base_regular_price );
					update_post_meta( $composite_id, '_sale_price', $base_sale_price );
				}

				// Delete product transients.
				wc_delete_product_transients( $composite_id );

				// Rename '_per_product_pricing_bto'.
				$wpdb->query( $wpdb->prepare( "
					UPDATE {$wpdb->postmeta}
					SET meta_key = '_bto_per_product_pricing'
					WHERE meta_key = '_per_product_pricing_bto'
					AND post_id = %d
				", $composite_id ) );

				// Rename '_per_product_pricing_bto'.
				$wpdb->query( $wpdb->prepare( "
					UPDATE {$wpdb->postmeta}
					SET meta_key = '_bto_per_product_shipping'
					WHERE meta_key = '_per_product_shipping_bto'
					AND post_id = %d
				", $composite_id ) );

			}
		}
	}
}

function wc_cp_update_370_db_version() {
	WC_CP_Install::update_db_version( '3.7.0' );
}

function wc_cp_update_380_main( $updater = false ) {

	global $wpdb;

	$composite_term = get_term_by( 'slug', 'composite', 'product_type' );

	if ( $composite_term ) {

		$composites = $wpdb->get_results( $wpdb->prepare( "
			SELECT DISTINCT posts.ID AS composite_id FROM {$wpdb->posts} AS posts
			LEFT JOIN {$wpdb->term_relationships} AS rel ON ( posts.ID = rel.object_id )
			LEFT JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id AND postmeta.meta_key = '_bto_base_price'
			WHERE rel.term_taxonomy_id = %d
			AND posts.post_type = 'product'
			AND postmeta.meta_value IS NULL
		", $composite_term->term_taxonomy_id ) );

		if ( ! empty( $composites ) ) {
			foreach ( $composites as $index => $composite ) {

				// Make sure we are nowhere close to memory & PHP timeout limits - check state every 20 migrated products.
				if ( is_object( $updater ) ) {
					if ( $index % 20 === 19 ) {
						if ( $updater->time_exceeded() || $updater->memory_exceeded() ) {
							return -1;
						}
					}
				}

				$composite_id = (int) $composite->composite_id;

				$price         = get_post_meta( $composite_id, '_price', true );
				$regular_price = get_post_meta( $composite_id, '_regular_price', true );
				$sale_price    = get_post_meta( $composite_id, '_sale_price', true );

				update_post_meta( $composite_id, '_bto_base_price', $price );
				delete_post_meta( $composite_id, '_base_price' );

				update_post_meta( $composite_id, '_bto_base_regular_price', $regular_price );
				delete_post_meta( $composite_id, '_base_regular_price' );

				update_post_meta( $composite_id, '_bto_base_sale_price', $sale_price );
				delete_post_meta( $composite_id, '_base_sale_price' );
			}
		}
	}
}

function wc_cp_update_380_delete_unused_meta() {

	global $wpdb;

	// Delete unused meta.
	$wpdb->query( "
		DELETE FROM {$wpdb->postmeta}
		WHERE meta_key = '_wc_sw_min_price'
	" );

	wc_cp_update_370_delete_unused_meta();
}

function wc_cp_update_380_db_version() {
	WC_CP_Install::update_db_version( '3.8.0' );
}
