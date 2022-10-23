<?php
/**
 * Free Gift Coupos DB update functions
 *
 * @package  WooCommerce Free Gift Coupons
 * @since    2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wc_fgc_update_200() {

	global $wpdb;

	// Convert v1.X postmeta array to associative array.
	$v1_coupons = $wpdb->get_results( "
		SELECT DISTINCT posts.ID AS coupon_id FROM {$wpdb->posts} AS posts
		LEFT OUTER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id AND postmeta.meta_key = 'gift_ids'
		WHERE posts.post_type = 'shop_coupon'
		AND postmeta.meta_value IS NOT NULL
	" );

	if ( ! empty( $v1_coupons ) ) {

		foreach ( $v1_coupons as $v1_coupon ) {

			$coupon_id = $v1_coupon->coupon_id;

			$free_gifts = array();

			// Get the coupon object.
			$coupon = new WC_Coupon( $coupon_id );
			
			if ( ! is_wp_error( $coupon ) && $coupon->is_type( 'free_gift' ) ) {
				$free_gifts = $coupon->get_meta( 'gift_ids', true, 'edit' );
			}

			// Convert any comma delimited string meta into array.
			$free_gifts = is_string( $free_gifts ) ? explode( ',', $free_gifts ) : (array) $free_gifts;
			$free_gifts = array_map( 'absint', $free_gifts );

			$gift_data = array();

			if ( ! empty( $free_gifts ) ) {

				foreach ( $free_gifts as $free_gift_id ) {

					$gift_data[ $free_gift_id ] = array();

					$gift_product = wc_get_product( $free_gift_id );

					if ( is_a( $gift_product, 'WC_Product' ) ) {

						if ( $gift_product->get_parent_id() > 0 ) {
							$gift_data[ $free_gift_id ][ 'product_id' ]   = $gift_product->get_parent_id();
							$gift_data[ $free_gift_id ][ 'variation_id' ] = $free_gift_id;
						} else {
							$gift_data[ $free_gift_id ][ 'product_id' ]   = $free_gift_id;
							$gift_data[ $free_gift_id ][ 'variation_id' ] = 0;
						}

						$gift_data[ $free_gift_id ][ 'quantity' ] = 1;

					}

				}

			}

			// Update the meta data.
			$coupon->update_meta_data( '_wc_free_gift_coupon_data', $gift_data );
			
			// Rename free_gift_shipping meta so our meta keys are sharing a prefix.
			$free_gift_shipping = $coupon->get_meta( 'free_gift_shipping', true, 'edit' );
			$coupon->update_meta_data( '_wc_free_gift_coupon_free_shipping', wc_bool_to_string( $free_gift_shipping ) );

			// Save the new meta data.
			$coupon->save_meta_data();

			$wpdb->query( $wpdb->prepare( "
				DELETE FROM {$wpdb->postmeta}
				WHERE post_id=%s
				AND meta_key IN ( %s, %s )
			", $coupon_id, 'gift_ids', 'free_gift_shipping' ) );
			
		}
	}
}

function wc_fgc_update_200_db_version() {
	WC_Free_Gift_Coupons_Install::update_db_version( '2.0.0' );
}
