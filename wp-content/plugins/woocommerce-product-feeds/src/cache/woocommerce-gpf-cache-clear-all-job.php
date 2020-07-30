<?php

use Pimple\Container;

class WoocommerceGpfClearAllJob extends WoocommerceGpfAbstractCacheRebuildJob {

	/**
	 * @var string The action hook used for this job.
	 */
	protected $action_hook = 'woocommerce_product_feeds_cache_clear_all';

	/**
	 * Task controller.
	 *
	 * Clear existing cache items, and queue a full rebuild.
	 */
	public function task() {

		global $wpdb, $table_prefix;

		// Clear existing cache items.
		$sql = "DELETE
		          FROM {$table_prefix}wc_gpf_render_cache";
		$wpdb->query( $sql );

		// Queue a rebuild.
		as_schedule_single_action(
			null,
			'woocommerce_product_feeds_cache_rebuild_simple',
			[
				0,
				apply_filters( 'woocommerce_product_feeds_rebuild_chunk_limit_simple', 30 ),
				null,
			],
			'woocommerce-product-feeds'
		);
		as_schedule_single_action(
			null,
			'woocommerce_product_feeds_cache_rebuild_complex',
			[
				0,
				apply_filters( 'woocommerce_product_feeds_rebuild_chunk_limit_complex', 1 ),
				null,
			],
			'woocommerce-product-feeds'
		);
	}
}
