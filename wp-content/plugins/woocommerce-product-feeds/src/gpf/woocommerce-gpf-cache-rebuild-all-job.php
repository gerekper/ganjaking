<?php

class WoocommerceGpfRebuildAllJob extends WoocommerceGpfAbstractCacheRebuildJob {

	/**
	 * @var string
	 */
	protected $action = 'woocommerce_gpf_rebuild_all';

	/**
	 * Task controller.
	 *
	 * Takes care of processing the current sub-task, and either re-pushing back
	 * to the queue for the next sub-task or completing the item.
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed
	 */
	protected function task( $job ) {
		$this->set_optimisations();
		// Work out which sub-task to process.
		switch ( $job['task'] ) {
			case 'clear-all':
				return $this->clear_all( $job );
				break;
			case 'rebuild':
				return $this->rebuild( $job );
				break;
		}
		return false;
	}

	private function rebuild( $job ) {

		// Set the default progress indicators.
		$defaults = array(
			'offset' => 0,
			'limit'  => apply_filters( 'woocommerce_gpf_rebuild_chunk_limit', 1 ),
		);
		$job      = array_merge( $defaults, $job );

		// Grab the products
		$args = array(
			'status'  => array( 'publish' ),
			'type'    => array( 'simple', 'variable', 'composite', 'bundle' ),
			'limit'   => $job['limit'],
			'offset'  => $job['offset'],
			'orderby' => 'ID',
			'order'   => 'ASC',
			'return'  => 'ids',
		);
		$ids  = wc_get_products(
			apply_filters(
				'woocommerce_gpf_wc_get_products_args',
				$args,
				'feed'
			)
		);

		if ( empty( $ids ) ) {
			return false;
		}

		// Rebuild the cache for the items.
		foreach ( $ids as $id ) {
			$this->rebuild_item( $id );
		}

		// Queue up the next chunk.
		$job['offset'] += $job['limit'];

		return $job;
	}

	/**
	 *
	 * Clear existing cache items, and queue a full rebuild.
	 *
	 * @param  array  $item  The job config/state.
	 *
	 * @return array         The updated job config/state.
	 */
	private function clear_all( $item ) {
		global $wpdb, $table_prefix;
		$sql = "DELETE
		          FROM {$table_prefix}wc_gpf_render_cache";
		$wpdb->query( $sql );
		// Queue the next sub-task.
		$item['task'] = 'rebuild';
		return $item;
	}
}
