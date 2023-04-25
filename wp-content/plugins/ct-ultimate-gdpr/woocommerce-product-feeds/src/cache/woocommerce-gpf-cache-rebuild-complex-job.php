<?php

use Pimple\Container;

class WoocommerceGpfRebuildComplexJob extends WoocommerceGpfAbstractCacheRebuildBatchJob {
	/*
	 * @var string The action hook used for this job.
	 */
	protected $action_hook = 'woocommerce_product_feeds_cache_rebuild_complex';

	/**
	 * Array of product types that this job will handle.
	 *
	 * @var array
	 */
	protected $product_types = [ 'variable' ];
}
