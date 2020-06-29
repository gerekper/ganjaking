<?php

class WoocommerceGpfRebuildTermJob extends WoocommerceGpfAbstractCacheRebuildJob {

	/**
	 * @var string
	 */
	protected $action = 'woocommerce_gpf_rebuild_term';

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
		// Set the default progress indicators.
		$defaults = array(
			'offset' => 0,
			'limit'  => apply_filters( 'woocommerce_gpf_rebuild_chunk_limit', 30 ),
		);
		$job      = array_merge( $defaults, $job );

		// Check that this is a term that might be attached to products.
		// We're done if not.
		$taxonomy_names = get_object_taxonomies( 'product', 'names' );
		if ( ! in_array( $job['taxonomy'], $taxonomy_names ) ) {
			return false;
		}

		// Query for all affected products, and queue up a rebuild for each
		// of them.
		$args = array(
			'status'  => array( 'publish' ),
			'type'    => array( 'simple', 'variable', 'composite', 'bundle' ),
			'limit'   => $job['limit'],
			'offset'  => $job['offset'],
			'orderBy' => 'date',
			'order'   => 'ASC',
			'return'  => 'ids',
		);

		// If the term is a category, or tag, restrict to just the linked
		// products. If not, we'll basically refresh all.
		if ( 'product_cat' === $job['taxonomy'] ) {
			$term = get_term( $job['term_id'] );
			if ( ! $term ) {
				return false;
			}
			$args['category'] = array( $term->slug );
		} elseif ( 'product_tag' === $job['taxonomy'] ) {
			$term = get_term( $job['term_id'] );
			if ( ! $term ) {
				return false;
			}
			$args['tag'] = array( $term->slug );
		}

		$ids = wc_get_products(
			apply_filters(
				'woocommerce_gpf_wc_get_products_args',
				$args,
				'feed'
			)
		);

		// See if we're done.
		if ( empty( $ids ) ) {
			return false;
		}

		$cnt = 0;

		// Get the rebuild queue.
		$product_rebuild_job = new WoocommerceGpfRebuildProductJob();

		// Rebuild the cache for the items.
		foreach ( $ids as $id ) {
			$product_rebuild_job
				->push_to_queue( array( 'post_id' => $id ) );
				$cnt++;
		}

		// Dispatch the jobs.
		$product_rebuild_job->save()->dispatch();

		// If we got fewer products than we asked for then we're done.
		if ( $cnt < $job['limit'] ) {
			return false;
		}

		// Otherwise, queue up the next chunk.
		$job['offset'] += $job['limit'];

		return $job;
	}
}
