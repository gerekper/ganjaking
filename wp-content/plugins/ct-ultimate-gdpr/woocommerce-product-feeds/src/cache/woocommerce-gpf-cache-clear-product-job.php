<?php

use Pimple\Container;

class WoocommerceGpfClearProductJob extends WoocommerceGpfAbstractCacheRebuildJob {

	/**
	 * @var string The action hook used for this job.
	 */
	protected $action_hook = 'woocommerce_product_feeds_cache_clear_product';

	/**
	 * @var int The number of arguments our hooked function expects.
	 */
	protected $action_hook_arg_count = 1;

	/**
	 * Process the rebuild.
	 *
	 * @param array $post_id The post ID to process.
	 */
	public function task( $post_id ) {
		global $wpdb, $table_prefix;
		$sql = "DELETE
		          FROM {$table_prefix}wc_gpf_render_cache
				 WHERE post_id = %d";
		$wpdb->query( $wpdb->prepare( $sql, $post_id ) );
	}
}
