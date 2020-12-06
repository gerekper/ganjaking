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
	 * @var Container
	 */
	protected $container;

	/**
	 * @var WoocommerceGpfDebugService
	 */
	protected $debug;

	/**
	 * Constructor.
	 *
	 * Store dependencies, and attach action callback.
	 *
	 * @param WoocommerceGpfCommon $woocommerce_gpf_common
	 * @param WoocommerceGpfCache $woocommerce_gpf_cache
	 * @param WoocommerceGpfDebugService $debug
	 * @param Container $container
	 */
	public function __construct(
		WoocommerceGpfCommon $woocommerce_gpf_common,
		WoocommerceGpfCache $woocommerce_gpf_cache,
		WoocommerceGpfDebugService $debug,
		Container $container
	) {
		$this->common    = $woocommerce_gpf_common;
		$this->cache     = $woocommerce_gpf_cache;
		$this->debug     = $debug;
		$this->container = $container;
		add_action( $this->action_hook, [ $this, 'task' ], 10, $this->action_hook_arg_count );
	}

	/**
	 * Initialise the classes we need to perform rebuilds, and set up some optimisations.
	 */
	public function initialise_rebuild() {
		global $wpdb;

		$feed_types                     = $this->common->get_feed_types();
		$this->feed_formats             = array();
		$this->non_product_feed_formats = array();
		foreach ( array_keys( $feed_types ) as $feed_id ) {
			$class                           = $feed_types[ $feed_id ]['class'];
			$this->feed_handlers[ $feed_id ] = $this->container[ $class ];
			if ( 'product' === $feed_types[ $feed_id ]['type'] ) {
				$this->feed_formats[] = $feed_id;
			} else {
				$this->non_product_feed_formats[] = $feed_id;
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

		/**
		 * Handles rebuilds for "product" feed types.
		 */
		switch ( $woocommerce_product->get_type() ) {
			case 'simple':
				return $this->process_simple_product( $woocommerce_product );
				break;
			case 'variable':
				if (
				apply_filters(
					'woocommerce_gpf_include_variations',
					! empty( $settings['include_variations'] ),
					$woocommerce_product
				)
				) {
					return $this->process_variable_product( $woocommerce_product );
				} else {
					return $this->process_simple_product( $woocommerce_product );
				}
				break;
			default:
				// Unknown product type. Try and process as a simple product.
				return $this->process_simple_product( $woocommerce_product );
				break;
		}
	}

	/**
	 * Process a simple product.
	 *
	 * @todo This is mostly a rough copy of the code in the frontend class. The
	 * logic could do with centralising.
	 *
	 * @return bool
	 */
	protected function process_simple_product( $woocommerce_product ) {

		foreach ( $this->feed_formats as $feed_format ) {
			// Do not rebuild for feeds that aren't enabled.
			if ( ! $this->common->is_feed_enabled( $feed_format ) ) {
				continue;
			}
			// Construct the data for this item.
			$feed_item = new WoocommerceGpfFeedItem(
				$woocommerce_product,
				$woocommerce_product,
				$feed_format,
				$this->common,
				$this->debug
			);
			if ( $feed_item->is_excluded() ) {
				$this->cache->store( $feed_item->ID, $feed_format, '' );
				continue;
			}
			// Allow other plugins to modify the item before its rendered to the feed
			$feed_item = apply_filters( 'woocommerce_gpf_feed_item', $feed_item, $woocommerce_product );
			$feed_item = apply_filters( 'woocommerce_gpf_feed_item_' . $feed_format, $feed_item, $woocommerce_product );

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
	 * @todo This is mostly a rough copy of the code in the frontend class. The
	 * logic could do with centralising.
	 *
	 * @return bool
	 */
	protected function process_variable_product( $woocommerce_product ) {

		// Check if the whole product is excluded.
		$feed_item = new WoocommerceGpfFeedItem(
			$woocommerce_product,
			$woocommerce_product,
			'google',
			$this->common,
			$this->debug
		);
		if ( $feed_item->is_excluded() ) {
			foreach ( $this->feed_formats as $feed_format ) {
				// Do not rebuild for feeds that aren't enabled.
				if ( ! $this->common->is_feed_enabled( $feed_format ) ) {
					continue;
				}
				$this->cache->store( $woocommerce_product->get_id(), $feed_format, '' );
			}

			return true;
		}

		$variation_ids = $woocommerce_product->get_children();
		foreach ( $this->feed_formats as $feed_format ) {
			// Do not rebuild for feeds that aren't enabled.
			if ( ! $this->common->is_feed_enabled( $feed_format ) ) {
				continue;
			}
			$output = '';
			foreach ( $variation_ids as $variation_id ) {
				// Get the variation product.
				$variation_product = wc_get_product( $variation_id );
				$feed_item         = new WoocommerceGpfFeedItem(
					$variation_product,
					$woocommerce_product,
					$feed_format,
					$this->common,
					$this->debug
				);
				// Skip to the next if this variation isn't to be included.
				if ( $feed_item->is_excluded() ) {
					continue;
				}
				// Allow other plugins to modify the item before its rendered to the feed
				$feed_item = apply_filters( 'woocommerce_gpf_feed_item', $feed_item, $woocommerce_product );
				$feed_item = apply_filters( 'woocommerce_gpf_feed_item_' . $feed_format, $feed_item, $woocommerce_product );

				// Render it.
				$output .= $this->feed_handlers[ $feed_format ]->render_item( $feed_item );
			}
			$this->cache->store( $woocommerce_product->get_id(), $feed_format, $output );
		}
		return true;
	}
}
