<?php

use Pimple\Container;

abstract class WoocommerceGpfAbstractCacheRebuildJob {

	/**
	 * Array of product feed formats which will be rebuilt.
	 */
	private $feed_formats;

	/**
	 * Array of non-product feed formats which will be rebuilt.
	 */
	private $non_product_feed_formats;

	/**
	 * Instances of the feed handling classes.
	 */
	private $feed_handlers;

	/**
	 * @var string  The hook used for this job.
	 */
	protected $action_hook;

	/**
	 * @var int The number of arguments our hooked function expects.
	 */
	protected $action_hook_arg_count = 0;

	/**
	 * @var WoocommerceGpfCommon
	 */
	protected $common;

	/**
	 * @var WoocommerceGpfCache
	 */
	private $cache;

	/**
	 * @var WoocommerceProductFeedsFeedItemFactory
	 */
	protected $feed_item_factory;

	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * Constructor.
	 *
	 * Store dependencies, and attach action callback.
	 *
	 * @param WoocommerceGpfCommon $woocommerce_gpf_common
	 * @param WoocommerceGpfCache $woocommerce_gpf_cache
	 * @param WoocommerceProductFeedsFeedItemFactory $feed_item_factory
	 * @param Container $container
	 */
	public function __construct(
		WoocommerceGpfCommon $woocommerce_gpf_common,
		WoocommerceGpfCache $woocommerce_gpf_cache,
		WoocommerceProductFeedsFeedItemFactory $feed_item_factory,
		Container $container
	) {
		$this->common            = $woocommerce_gpf_common;
		$this->cache             = $woocommerce_gpf_cache;
		$this->feed_item_factory = $feed_item_factory;
		$this->container         = $container;
		add_action( $this->action_hook, [ $this, 'task' ], 10, $this->action_hook_arg_count );
	}

	/**
	 * Initialise the classes we need to perform rebuilds, and set up some optimisations.
	 *
	 * @SuppressWarnings(PHPMD.ErrorControlOperator)
	 */
	public function initialise_rebuild() {
		global $wpdb;

		$feed_types                     = $this->common->get_feed_types();
		$this->feed_formats             = [];
		$this->non_product_feed_formats = [];

		// Build the feed handlers array.
		foreach ( array_keys( $feed_types ) as $feed_id ) {
			$class                           = $feed_types[ $feed_id ]['class'];
			$this->feed_handlers[ $feed_id ] = $this->container[ $class ];
		}
		// TODO config repository should be passed in as a dependency.
		$config_repository = $this->container['WoocommerceProductFeedsFeedConfigRepository'];
		$all_feed_formats  = $config_repository->get_active_feed_formats();
		foreach ( $all_feed_formats as $feed_format ) {
			if ( 'product' === $feed_types[ $feed_format ]['type'] ) {
				$this->feed_formats[] = $feed_format;
			} else {
				$this->non_product_feed_formats[] = $feed_format;
			}
		}

		// Cater for large stores.
		$wpdb->hide_errors();
		@set_time_limit( 0 );
		while ( ob_get_level() ) {
			@ob_end_clean();
		}
		// Disable term ordering by Advanced Taxonomy Terms Order from
		// (http://www.nsp-code.com) as it has horrible performance
		// characteristics.
		add_filter( 'atto/ignore_get_object_terms', '__return_true', 9999 );
		if ( has_filter( 'terms_clauses', 'to_terms_clauses' ) ) {
			remove_filter( 'terms_clauses', 'to_terms_clauses', 99 );
		} else {
			add_action(
				'plugins_loaded',
				function () {
					remove_filter( 'terms_clauses', 'to_terms_clauses', 99 );
				}
			);
		}
	}

	/**
	 * Cancel Process
	 *
	 * Stop processing all queue items and clear jobs of this type.
	 */
	public function cancel_all() {
		as_unschedule_all_actions( $this->action_hook );
	}

	/**
	 * Rebuild a specific item.
	 *
	 * @param $product_id
	 *
	 * @return bool|void
	 */
	protected function rebuild_item( $product_id ) {
		// Load the settings.
		$settings = get_option( 'woocommerce_gpf_config', array() );

		$woocommerce_product = wc_get_product( $product_id );
		if ( empty( $woocommerce_product ) ) {
			// It is not a product. We are done.
			return;
		}

		/**
		 * Handle rebuild for non-product feed types.
		 */
		foreach ( $this->non_product_feed_formats as $feed_id ) {
			$this->feed_handlers[ $feed_id ]->rebuild_item( $woocommerce_product );
		}

		$include_variations = apply_filters(
			'woocommerce_gpf_include_variations',
			! empty( $settings['include_variations'] ),
			$woocommerce_product
		);
		if ( $woocommerce_product instanceof WC_Product_Variable &&
			 $include_variations ) {
			return $this->process_variable_product( $woocommerce_product );
		}

		return $this->process_simple_product( $woocommerce_product );
	}

	/**
	 * Process a simple product.
	 *
	 * @return bool
	 * @todo This is mostly a rough copy of the code in the frontend class. The
	 * logic could do with centralising.
	 *
	 */
	protected function process_simple_product( $woocommerce_product ) {

		foreach ( $this->feed_formats as $feed_format ) {
			// Construct the data for this item.
			$feed_item = $this->feed_item_factory->create( $feed_format, $woocommerce_product, $woocommerce_product );
			if ( $feed_item->is_excluded() ) {
				$this->cache->store( $feed_item->ID, $feed_format, '' );
				continue;
			}
			// Render it.
			$output = $this->feed_handlers[ $feed_format ]->render_item( $feed_item );

			// Store it to the cache.
			$this->cache->store( $feed_item->ID, $feed_format, $output );
		}

		return true;
	}

	/**
	 * Process a variable product.
	 *
	 * @return bool
	 * @todo This is mostly a rough copy of the code in the frontend class. The
	 * logic could do with centralising.
	 *
	 */
	protected function process_variable_product( $woocommerce_product ) {

		// Check if the whole product is excluded.
		$feed_item = $this->feed_item_factory->create( 'google', $woocommerce_product, $woocommerce_product );
		if ( $feed_item->is_excluded() ) {
			foreach ( $this->feed_formats as $feed_format ) {
				$this->cache->store( $woocommerce_product->get_id(), $feed_format, '' );
			}

			return true;
		}

		$variation_ids = $woocommerce_product->get_children();
		foreach ( $this->feed_formats as $feed_format ) {
			$output = '';
			foreach ( $variation_ids as $variation_id ) {
				// Get the variation product.
				$variation_product = wc_get_product( $variation_id );
				if ( ! $variation_product ) {
					continue;
				}
				$feed_item = $this->feed_item_factory->create(
					$feed_format,
					$variation_product,
					$woocommerce_product
				);
				// Skip to the next if this variation isn't to be included.
				if ( $feed_item->is_excluded() ) {
					continue;
				}
				// Render it.
				$output .= $this->feed_handlers[ $feed_format ]->render_item( $feed_item );
			}
			$this->cache->store( $woocommerce_product->get_id(), $feed_format, $output );
		}

		return true;
	}
}
