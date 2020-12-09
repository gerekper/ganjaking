<?php

/**
 * woocommerce_gpf_structured_data
 *
 * Enriches the on-page microdata based on Google Product Feed data values.
 */
class WoocommerceProductFeedsExpandedStructuredDataCacheInvalidator {

	/**
	 * @var array
	 */
	private $variation_deletion_cache = [];

	public function initialise() {
		// Cache invalidation actions for simple products.
		add_action( 'woocommerce_update_product', [ $this, 'invalidate_schema_cache' ], 90, 2 );

		// Cache invalidation for variations.
		add_action( 'woocommerce_new_product_variation', [ $this, 'invalidate_schema_cache' ], 90, 2 );
		add_action( 'woocommerce_update_product_variation', [ $this, 'invalidate_schema_cache' ], 90, 2 );
		add_action( 'woocommerce_trash_product_variation', [ $this, 'invalidate_schema_cache_trashed_variation' ], 90 );
		add_action(
			'woocommerce_before_delete_product_variation',
			[ $this, 'invalidate_schema_cache_before_variation_delete' ],
			90
		);
		add_action(
			'woocommerce_delete_product_variation',
			[ $this, 'invalidate_schema_cache_after_variation_delete' ],
			90
		);

		// Cache invalidation for other cases.
		add_action( 'woocommerce_update_options_gpf', array( $this, 'invalidate_schema_indirectly' ), 90 );
		add_action( 'edited_term', [ $this, 'invalidate_schema_indirectly' ], 90 );
		add_action( 'delete_term', [ $this, 'invalidate_schema_indirectly' ], 90 );
	}

	// Store a timestamp to invalidate any items older than this.
	public function invalidate_schema_indirectly() {
		update_option( 'woocommerce_gpf_schema_min_timestamp_validity', time() );
	}

	/**
	 * Handle the fact that a variation is trashed by invalidating the cache of its parent.
	 *
	 * @param $variation_id
	 */
	public function invalidate_schema_cache_trashed_variation( $variation_id ) {
		// $variation_id is a trashed product but should still exist.
		$variation = wc_get_product( $variation_id );
		if ( ! $variation ) {
			return;
		}
		// Find parent.
		$parent_id         = $variation->get_parent_id();
		$parent_wc_product = wc_get_product( $parent_id );
		if ( ! $parent_wc_product ) {
			return;
		}
		// Call invalidate_schema_cache() for parent product
		$this->invalidate_schema_cache( $parent_id, $parent_wc_product );

	}

	/**
	 * Handle the fact that a variation is about to be deleted.
	 *
	 * Notes the variation_id => product_id mapping so cache can be invalidated post-deletion.
	 *
	 * @param $variation_id
	 */
	public function invalidate_schema_cache_before_variation_delete( $variation_id ) {
		$variation = wc_get_product( $variation_id );
		if ( ! $variation ) {
			return;
		}
		// Find parent, and store it.
		$parent_id = $variation->get_parent_id();
		if ( ! empty( $parent_id ) ) {
			$this->variation_deletion_cache[ $variation_id ] = $parent_id;
		}
	}

	/**
	 * Handle the fact that a variation has been deleted.
	 *
	 * @param $variation_id
	 *
	 * @see invalidate_schema_cache_before_variation_delete
	 *
	 */
	public function invalidate_schema_cache_after_variation_delete( $variation_id ) {
		if ( ! isset( $this->variation_deletion_cache[ $variation_id ] ) ) {
			return;
		}
		$parent_id         = $this->variation_deletion_cache[ $variation_id ];
		$parent_wc_product = wc_get_product( $parent_id );
		$this->invalidate_schema_cache( $parent_id, $parent_wc_product );
	}

	/**
	 * Remove the cache for a product.
	 *
	 * @param int $product_id
	 * @param \WC_Product $wc_product
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function invalidate_schema_cache( $product_id, $wc_product ) {
		if ( ! $wc_product ) {
			return;
		}
		$wc_product->delete_meta_data( 'woocommerce_gpf_schema_cache' );
		$wc_product->delete_meta_data( 'woocommerce_gpf_schema_cache_timestamp' );
		$wc_product->save_meta_data();
	}
}
