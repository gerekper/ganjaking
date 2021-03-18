<?php
/**
 * class-woocommerce-product-search-product-processor.php
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
 * @since 3.6.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product processor.
 */
class WooCommerce_Product_Search_Product_Processor {

	/**
	 * Variation processing threshold.
	 *
	 * @var int
	 */
	const THRESHOLD = 3;

	/**
	 * Index action priority.
	 *
	 * @var int
	 */
	const INDEX_ACTION_PRIORITY = 10000;

	/**
	 * Keep track of updated IDs.
	 *
	 * @var array
	 */
	private static $updated_ids = array();

	/**
	 * Keep track of updated IDs.
	 *
	 * @var array
	 */
	private static $deleted_ids = array();

	/**
	 * Hooks.
	 */
	public static function init() {

		add_action( 'woocommerce_new_product', array( __CLASS__, 'woocommerce_new_product' ), self::INDEX_ACTION_PRIORITY, 2 );

		add_action( 'woocommerce_update_product', array( __CLASS__, 'woocommerce_update_product' ), self::INDEX_ACTION_PRIORITY, 2 );

		add_action( 'woocommerce_delete_product', array( __CLASS__, 'woocommerce_delete_product' ), self::INDEX_ACTION_PRIORITY );

	}

	/**
	 * Handle new product.
	 *
	 * @param int $product_id
	 * @param WC_Product $product
	 */
	public static function woocommerce_new_product( $product_id, $product ) {
		self::process( $product_id );
	}

	/**
	 * Handle updated product.
	 *
	 * @param int $product_id
	 * @param WC_Product $product
	 */
	public static function woocommerce_update_product( $product_id, $product ) {

		$product_id = intval( $product_id );
		if ( !in_array( $product_id, self::$updated_ids ) ) {
			self::$updated_ids[] = $product_id;
			self::process( $product_id );
		}
	}

	/**
	 * Handle deleted product.
	 *
	 * @param int $post_id
	 */
	public static function deleted_post( $post_id = null ) {
		$post_id = intval( $post_id );
		if ( !in_array( $post_id, self::$deleted_ids ) ) {
			self::$deleted_ids[] = $post_id;
			$post_type = get_post_type( $post_id );
			if ( $post_type === 'product' ) {
				$indexer = new WooCommerce_Product_Search_Indexer();
				$indexer->purge( $post_id );
				unset( $indexer );
			}
		}
	}

	/**
	 * Handle deleted product.
	 *
	 * @param int $product_id
	 */
	public static function woocommerce_delete_product( $product_id ) {
		self::deleted_post( $product_id );
	}

	public static function process( $post_id = null ) {

		global $wpdb;

		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE || wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) ) {
		} else {
			$post_type = get_post_type( $post_id );
			if ( $post_type === 'product' ) {
				$guardian = new WooCommerce_Product_Search_Guardian();
				$guardian->start();
				$indexer = new WooCommerce_Product_Search_Indexer();
				$post_status = get_post_status( $post_id );
				switch ( $post_status ) {
					case 'publish' :
					case 'pending' :
					case 'draft' :
					case 'private' :
						$indexer->index( $post_id );

						$variation_ids = $wpdb->get_col( $wpdb->prepare(
							"SELECT ID FROM $wpdb->posts WHERE post_parent = %d AND post_type = 'product_variation'",
							intval( $post_id )
						) );
						if ( is_array( $variation_ids ) ) {
							$variation_ids = array_unique( array_map( 'intval', $variation_ids ) );
							$threshold = is_numeric( WPS_DEFER_VARIATIONS_THRESHOLD ) ? intval( WPS_DEFER_VARIATIONS_THRESHOLD ) : self::THRESHOLD;
							if ( $threshold < 0 ) {
								$threshold = 0;
							}
							if ( $threshold > 0 ) {
								$processed = 0;
								$total = count( $variation_ids );
								foreach( $variation_ids as $variation_id ) {
									if ( $guardian->is_ok() ) {
										$indexer->index( $variation_id );
										$processed++;
										if ( $processed >= $threshold && $total > $processed ) {
											wps_log_info(
												'WooCommerce Product Search - ' .
												esc_html__( 'Info', 'woocommerce-product-search' ) .
												' : ' .
												sprintf(
													esc_html__( 'Deferred further variation processing on reaching threshold (%d).', 'woocommerce-product-search' ),
													$threshold
												)
											);
											break;
										}
									} else {
										wps_log_info(
											'WooCommerce Product Search - ' .
											esc_html__( 'Info', 'woocommerce-product-search' ) .
											' : ' .
											esc_html__( 'Deferred variation processing to avoid PHP resource limit issues.', 'woocommerce-product-search' )
										);
										break;
									}
								}
							}
						}
						break;
					default :
						$indexer->purge( $post_id );
				}
				unset( $indexer );
			}
		}
	}

}
WooCommerce_Product_Search_Product_Processor::init();
