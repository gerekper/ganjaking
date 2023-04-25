<?php

use Pimple\Container;

class WoocommerceGpfAbstractCacheRebuildBatchJob extends WoocommerceGpfAbstractCacheRebuildJob {

	/**
	 * Array of product types that this job will handle.
	 *
	 * @var array
	 */
	protected $product_types = [];

	/**
	 * @var int The number of arguments our hooked function expects.
	 */
	protected $action_hook_arg_count = 3;

	/**
	 * @var array Temporary storage of term filtering requirements.
	 */
	protected $term_filter = null;

	/**
	 * Task controller.
	 *
	 * Takes care of processing the current sub-task, and either re-pushing back
	 * to the queue for the next sub-task or completing the item.
	 *
	 * @param int $offset
	 * @param int $limit
	 * @param $term_filter
	 */
	public function task( $offset, $limit, $term_filter = null ) {

		$this->initialise_rebuild();
		$this->term_filter = $term_filter;

		// Grab the products
		$args = array(
			'status'  => array( 'publish' ),
			'type'    => $this->product_types,
			'limit'   => $limit,
			'offset'  => $offset,
			'orderby' => 'ID',
			'order'   => 'ASC',
			'return'  => 'ids',
		);

		add_filter( 'woocommerce_product_data_store_cpt_get_products_query', [ $this, 'filter_query' ], 10, 2 );
		$ids = wc_get_products(
			apply_filters(
				'woocommerce_gpf_wc_get_products_args',
				$args,
				get_class( $this )
			)
		);
		remove_filter( 'woocommerce_product_data_store_cpt_get_products_query', [ $this, 'filter_query' ], 10 );

		// Rebuild the cache for the items.
		foreach ( $ids as $id ) {
			$this->rebuild_item( $id );
		}

		// Bail if we've completed.
		if ( count( $ids ) < $limit ) {
			return;
		}

		// Queue up the next chunk.
		as_schedule_single_action(
			null,
			$this->action_hook,
			[
				$offset + $limit,
				$limit,
				$term_filter,
			],
			'woocommerce-product-feeds'
		);
	}

	/**
	 * Handle requirement to filter by term attachment.
	 *
	 * @param array $query - Args for WP_Query.
	 * @param array $query_vars - Query vars from WC_Product_Query.
	 *
	 * @return array modified $query
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function filter_query( $query, $query_vars ) {
		if ( empty( $this->term_filter['taxonomy'] ) ||
			 empty( $this->term_filter['term_id'] ) ) {
			return $query;
		}
		$query['tax_query'][] = array(
			'taxonomy' => $this->term_filter['taxonomy'],
			'field'    => 'term_id',
			'terms'    => $this->term_filter['term_id'],
		);

		return $query;
	}
}
