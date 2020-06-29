<?php

class WoocommerceGpfRebuildProductJob extends WoocommerceGpfAbstractCacheRebuildJob {

	/**
	 * @var string
	 */
	protected $action = 'woocommerce_gpf_rebuild_product';

	/**
	 * Process the rebuild.
	 *
	 * @param  array $job  The job to process.
	 */
	protected function task( $job ) {
		$this->set_optimisations();
		if ( empty( $job['post_id'] ) ) {
			return false;
		}
		$post = get_post( $job['post_id'] );
		if ( $post && 'product' !== $post->post_type ) {
			// It exists, but is not a product. We are done.
			return false;
		}

		// If we get here either it exists, and is a product, or has been
		// deleted.
		// If it's deleted, or trashed, we just clear down the cache for the
		// post, otherwise we rebuild the cache for it.
		if ( $post && 'trash' !== $post->post_status ) {
			$this->rebuild_item( $job['post_id'] );
		} else {
			$this->drop_post_cache( $job['post_id'] );
		}
		return false;
	}

	/**
	 * Clear existing cache item.
	 *
	 * @param  int  $post_id The post ID to drop.
	 */
	private function drop_post_cache( $post_id ) {
		global $wpdb, $table_prefix;
		$sql = "DELETE
		          FROM {$table_prefix}wc_gpf_render_cache
				 WHERE post_id = %d";
		$wpdb->query( $wpdb->prepare( $sql, $post_id ) );
	}

}
