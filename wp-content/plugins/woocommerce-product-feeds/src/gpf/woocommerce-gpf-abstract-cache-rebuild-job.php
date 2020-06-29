<?php

abstract class WoocommerceGpfAbstractCacheRebuildJob extends WP_Background_Process {

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
	 * Record whether we have had anything pushed onto our queue, and not saved.
	 * @var boolean
	 */
	private $dirty = false;

	/**
	 * Constructor.
	 */
	public function __construct() {
		global $woocommerce_gpf_common;
		parent::__construct();
		$this->cache                    = new WoocommerceGpfCache();
		$this->factory                  = new WC_Product_Factory();
		$feed_types                     = $woocommerce_gpf_common->get_feed_types();
		$this->feed_formats             = array();
		$this->non_product_feed_formats = array();
		foreach ( $feed_types as $feed_id => $feed_type ) {
			$class                           = $feed_types[ $feed_id ]['class'];
			$this->feed_handlers[ $feed_id ] = new $class();
			if ( 'product' === $feed_types[ $feed_id ]['type'] ) {
				$this->feed_formats[] = $feed_id;
			} else {
				$this->non_product_feed_formats[] = $feed_id;
			}
		}
		add_filter( $this->identifier . '_cron_interval', array( $this, 'set_cron_interval' ) );
	}

	/**
	 * We do one cron run per minute, not the default 5 minutes.
	 *
	 * @param int $interval The current interval
	 *
	 * @return int             The revised interval.
	 */
	public function set_cron_interval( $interval ) {
		return 1;
	}

	/**
	 * Cancel Process
	 *
	 * Stop processing all queue items, clear cronjob and delete batch.
	 *
	 */
	public function cancel_all() {
		while ( ! $this->is_queue_empty() ) {
			$batch = $this->get_batch();
			$this->delete( $batch->key );
		}
		$this->data = array();
		$this->save();
		wp_clear_scheduled_hook( $this->cron_hook_identifier );
	}

	protected function set_optimisations() {

		global $wpdb;

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
			remove_filter( 'terms_clauses', 'to_terms_clauses', 99, 3 );
		} else {
			add_action( 'plugins_loaded', array( $this, 'remove_atto_filter' ) );
		}
	}

	public function remote_atto_filter() {
		remove_filter( 'terms_clauses', 'to_terms_clauses', 99, 3 );
	}

	protected function rebuild_item( $id ) {

		// Load the settings.
		$settings            = get_option( 'woocommerce_gpf_config', array() );
		$woocommerce_product = wc_get_product( $id );

		/**
		 * Handle rebuild for non-product feed types.
		 */
		foreach ( $this->non_product_feed_formats as $feed_id ) {
			$this->feed_handlers[ $feed_id ]->rebuild_item( $id, $woocommerce_product );
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
			case 'composite':
				return $this->process_composite_product( $woocommerce_product );
				break;
			case 'bundle':
				return $this->process_bundle_product( $woocommerce_product );
				break;
			default:
				break;
		}
	}

	/**
	 * Process a simple product.
	 *
	 * @todo This is mostly a rough copy of the code in the frontend class. The
	 * logic could do with centralising.
	 */
	protected function process_simple_product( $woocommerce_product ) {

		global $woocommerce_gpf_common;

		foreach ( $this->feed_formats as $feed_format ) {
			// Do not rebuild for feeds that aren't enabled.
			if ( ! $woocommerce_gpf_common->is_feed_enabled( $feed_format ) ) {
				continue;
			}
			// Construct the data for this item.
			$feed_item = new WoocommerceGpfFeedItem( $woocommerce_product, $woocommerce_product, $feed_format, $woocommerce_gpf_common );
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
	 */
	protected function process_variable_product( $woocommerce_product ) {

		global $woocommerce_gpf_common;

		// Check if the whole product is excluded.
		$feed_item = new WoocommerceGpfFeedItem( $woocommerce_product, $woocommerce_product, 'google', $woocommerce_gpf_common );
		if ( $feed_item->is_excluded() ) {
			foreach ( $this->feed_formats as $feed_format ) {
				// Do not rebuild for feeds that aren't enabled.
				if ( ! $woocommerce_gpf_common->is_feed_enabled( $feed_format ) ) {
					continue;
				}
				$this->cache->store( $woocommerce_product->get_id(), $feed_format, '' );
			}

			return false;
		}

		$variations = $woocommerce_product->get_available_variations();
		foreach ( $this->feed_formats as $feed_format ) {
			// Do not rebuild for feeds that aren't enabled.
			if ( ! $woocommerce_gpf_common->is_feed_enabled( $feed_format ) ) {
				continue;
			}
			$output = '';
			foreach ( $variations as $variation ) {
				// Get the variation product.
				$variation_id      = $variation['variation_id'];
				$variation_product = $this->factory->get_product( $variation_id );
				$feed_item         = new WoocommerceGpfFeedItem( $variation_product, $woocommerce_product, $feed_format, $woocommerce_gpf_common );
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

	/**
	 * Process a composite product.
	 *
	 * @param object $woocommerce_product WooCommerce Product Object
	 *
	 * @return bool                          True if one or more products were output, false
	 *                                       otherwise.
	 */
	protected function process_composite_product( $woocommerce_product ) {
		return $this->process_simple_product( $woocommerce_product );
	}

	/**
	 * Process a bundle product.
	 *
	 * @param object $woocommerce_product WooCommerce Product Object
	 *
	 * @return bool                          True if one or more products were output, false
	 *                                       otherwise.
	 */
	protected function process_bundle_product( $woocommerce_product ) {
		return $this->process_simple_product( $woocommerce_product );
	}

	/**
	 * Push an item onto the queue, and mark ourselves as dirty.
	 */
	public function push_to_queue( $data ) {
		$this->dirty = true;
		parent::push_to_queue( $data );

		return $this;
	}

	/**
	 * Save the queue out to the database, and mark ourselves as clean again.
	 */
	public function save() {
		parent::save();
		$this->dirty = false;

		return $this;
	}

	/**
	 * Save & dispatch this queue if it is marked as dirty.
	 */
	public function dispatch_if_dirty() {
		if ( $this->dirty ) {
			$this->save()->dispatch();

			return true;
		}

		return false;
	}

	public function get_all_batches() {
		global $wpdb;

		$table        = $wpdb->options;
		$column       = 'option_name';
		$key_column   = 'option_id';
		$value_column = 'option_value';

		if ( is_multisite() ) {
			$table        = $wpdb->sitemeta;
			$column       = 'meta_key';
			$key_column   = 'meta_id';
			$value_column = 'meta_value';
		}

		$key = $this->identifier . '_batch_%';

		return $wpdb->get_col(
			$wpdb->prepare(
				" SELECT option_value
				    FROM {$table}
				   WHERE {$column} LIKE %s
				ORDER BY {$key_column} ASC",
				$key
			)
		);
	}
}
