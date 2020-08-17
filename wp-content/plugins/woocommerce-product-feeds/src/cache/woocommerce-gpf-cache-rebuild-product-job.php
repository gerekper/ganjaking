<?php

use Pimple\Container;

class WoocommerceGpfRebuildProductJob extends WoocommerceGpfAbstractCacheRebuildJob {

	/**
	 * @var string The action hook used for this job.
	 */
	protected $action_hook = 'woocommerce_product_feeds_cache_rebuild_product';

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
		$this->initialise_rebuild();
		if ( ! $post_id ) {
			return;
		}
		$post = get_post( $post_id );

		// If we get here either it exists, and is a product, or has been
		// deleted.
		// If it's deleted, or trashed, we just clear down the cache for the
		// post, otherwise we rebuild the cache for it.
		if ( $post && 'trash' !== $post->post_status ) {
			$this->rebuild_item( $post_id );
		} else {
			$this->drop_post_cache( $post_id );
		}
	}

	/**
	 * Clear existing cache item.
	 *
	 * @param int $post_id The post ID to drop.
	 */
	private function drop_post_cache( $post_id ) {
		global $wpdb, $table_prefix;
		$sql = "DELETE
		          FROM {$table_prefix}wc_gpf_render_cache
				 WHERE post_id = %d";
		$wpdb->query( $wpdb->prepare( $sql, $post_id ) );
	}
}
