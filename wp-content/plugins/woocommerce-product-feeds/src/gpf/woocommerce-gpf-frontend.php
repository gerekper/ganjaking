<?php

use Pimple\Container;

/**
 * Frontend class.
 *
 * Handles grabbing the products and invoking the relevant feed class to render the feed.
 */
class WoocommerceGpfFrontend {

	/**
	 * @var WoocommerceGpfFeed
	 */
	protected $feed = null;

	/**
	 * @var array
	 */
	protected $settings = array();

	/**
	 * @var WoocommerceGpfCommon
	 */
	protected $common;

	/**
	 * @var WoocommerceGpfCache
	 */
	protected $cache;

	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * @var WoocommerceGpfDebugService
	 */
	protected $debug;

	/**
	 * @var WoocommerceProductFeedsFeedConfigFactory
	 */
	protected $feed_config_factory;

	/**
	 * @var WoocommerceProductFeedsFeedConfig
	 */
	protected $feed_config;

	/**
	 * @var WoocommerceProductFeedsFeedItemFactory
	 */
	protected $feed_item_factory;

	/**
	 * Constructor. Add filters if we have stuff to do
	 *
	 * @access public
	 *
	 * @param WoocommerceGpfCommon $woocommerce_gpf_common
	 * @param WoocommerceGpfCache $woocommerce_gpf_cache
	 * @param WoocommerceGpfDebugService $debug
	 * @param WoocommerceProductFeedsFeedItemFactory $feed_item_factory
	 * @param Container $container
	 */
	public function __construct(
		WoocommerceGpfCommon $woocommerce_gpf_common,
		WoocommerceGpfCache $woocommerce_gpf_cache,
		WoocommerceGpfDebugService $debug,
		WoocommerceProductFeedsFeedItemFactory $feed_item_factory,
		Container $container
	) {
		$this->common            = $woocommerce_gpf_common;
		$this->cache             = $woocommerce_gpf_cache;
		$this->debug             = $debug;
		$this->container         = $container;
		$this->feed_item_factory = $feed_item_factory;
	}

	/**
	 * Load the settings, and hook in so we can generate the feed.
	 *
	 * @param WoocommerceProductFeedsFeedConfig $feed_config
	 */
	public function initialise( $feed_config ) {
		// Store the config.
		$this->feed_config = $feed_config;
		// Get the info we need to look up the right class.
		$all_feed_types = $this->common->get_feed_types();
		// Load the settings.
		$this->settings = get_option( 'woocommerce_gpf_config', array() );
		// Look up the right class to handle rendering the feed.
		$class = $all_feed_types[ $this->feed_config->type ]['class'];

		// Add hooks for future processing.
		add_action( 'template_redirect', [ $this, 'render_product_feed' ], 15 );
		add_filter(
			'woocommerce_product_data_store_cpt_get_products_query',
			[
				$this,
				'limit_query_by_category',
			],
			10,
			2
		);
		add_filter( 'woocommerce_gpf_store_info', [ $this, 'add_feed_url_to_store_info' ] );

		// Instantiate the feed class.
		$this->feed = $this->container[ $class ];
	}

	/**
	 * Add the feed URL to the store_info object.
	 * @param $store_info
	 *
	 * @return mixed
	 */
	public function add_feed_url_to_store_info( $store_info ) {
		$store_info->feed_url = $store_info->feed_url_base . 'woocommerce_gpf/' . $this->feed_config->id;

		return $store_info;
	}

	/**
	 * Set a number of optimisations to make sure the plugin is usable on lower end setups.
	 *
	 * We stop plugins trying to cache, or compress the output since that causes everything to be
	 * held in memory and causes memory issues.
	 *
	 * @SuppressWarnings(PHPMD.ErrorControlOperator)
	 */
	private function set_optimisations() {

		global $wpdb;

		// Don't cache feed.
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}
		if ( ! headers_sent() ) {
			header( 'Cache-Control: no-store, must-revalidate, max-age=0' );
		}

		// Cater for large stores.
		$wpdb->hide_errors();
		@set_time_limit( 0 );
		while ( ob_get_level() ) {
			@ob_end_clean();
		}

		// Disable term ordering by Advanced Taxonomy Terms Order (http://www.nsp-code.com)
		// as it has horrible performance characteristics.
		add_filter( 'atto/ignore_get_object_terms', '__return_true', 9999 );
		remove_filter( 'terms_clauses', 'to_terms_clauses', 99, 3 );
	}

	public function log_query_args( $args ) {
		$this->debug->log( 'Query args:' . wp_json_encode( $args, JSON_PRETTY_PRINT ) );

		return $args;
	}

	/**
	 * Generate the query function to use, and argument array.
	 *
	 * Identifies the query function to be used to retrieve products, either
	 * WordPress' get_posts(), or wc_get_products() depending on whether
	 * wc_get_products() is available.
	 *
	 * Also constructs the base arguments array to be passed to the query
	 * function.
	 *
	 * @param int $chunk_size The number of products to be retrieved per
	 *                             query.
	 *
	 * @return array               The arguments array.
	 */
	private function get_query_args( $chunk_size ) {

		$args = array(
			'status'  => array( 'publish' ),
			'type'    => array( 'simple', 'variable' ),
			'limit'   => $chunk_size,
			'offset'  => intval( $this->feed_config->start ),
			'orderby' => 'ID',
			'order'   => 'ASC',
		);
		if ( $this->cache->is_enabled() ) {
			$args['return'] = 'ids';
		}

		return apply_filters(
			'woocommerce_gpf_wc_get_products_args',
			$args,
			'feed'
		);
	}

	/**
	 * Apply category limits to the query.
	 *
	 * @param $query
	 * @param $query_vars
	 *
	 * @return mixed
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function limit_query_by_category( $query, $query_vars ) {
		$categories = array_map( 'intval', $this->feed_config->categories );
		if ( empty( $categories ) || '' === $this->feed_config->category_filter ) {
			return $query;
		}

		$tax_query = [
			'taxonomy' => 'product_cat',
			'field'    => 'term_id',
			'terms'    => $categories,
		];
		if ( 'except' === $this->feed_config->category_filter ) {
			$tax_query['operator'] = 'NOT IN';
		}
		$query['tax_query'][] = $tax_query;

		return $query;
	}

	/**
	 * Render the product feed requests - calls the sub-classes according
	 * to the feed required.
	 *
	 * @access public
	 */
	public function render_product_feed() {

		global $_wp_using_ext_object_cache;

		$this->set_optimisations();
		$this->feed->render_header();

		if ( $this->cache->is_enabled() ) {
			$chunk_size = 100;
		} else {
			$chunk_size = 10;
		}
		$chunk_size = apply_filters( 'woocommerce_gpf_chunk_size', $chunk_size, $this->cache->is_enabled() );

		$args = $this->get_query_args( $chunk_size );

		if ( $this->debug->debug_active() ) {
			add_filter( 'woocommerce_product_object_query_args', [ $this, 'log_query_args' ], 99999 );
		}

		$output_count = 0;
		$limit        = $this->feed_config->limit;

		// Note: $products will be:
		// - post IDs if the cache is enabled
		// - WC_Product objects if cache is disabled
		$products      = wc_get_products( $args );
		$product_count = count( $products );

		$this->debug->log( 'Retrieved %d products', [ $product_count ] );
		while ( $product_count ) {

			if ( $this->cache->is_enabled() ) {
				// Output any that we have in the cache.
				$outputs = $this->cache->fetch_multi( $products, $this->feed_config->type );
				foreach ( $products as $product_id ) {
					if ( ! empty( $outputs[ $product_id ] ) ) {
						$this->debug->log( 'Retrieved %d from cache', [ $product_id ] );
						echo $outputs[ $product_id ];
						$output_count++;
					} else {
						$this->debug->log( 'Retrieved empty record from cache for %d', [ $product_id ] );
					}
					if ( -1 !== $limit && $output_count >= $limit ) {
						$this->debug->log( '[%d] Reached limit (%d). Exiting.', [ __LINE__, $limit ] );
						break;
					}
				}
				// Remove any we got from the list to be generated.
				$products = array_diff( $products, array_keys( $outputs ) );
			}

			// Bail if we're done.
			if ( -1 !== $limit && $output_count >= $limit ) {
				$this->debug->log( '[%d] Reached limit (%d). Exiting.', [ __LINE__, $limit ] );
				break;
			}

			// If we have any still to generate, go do them.
			foreach ( $products as $product ) {
				if ( $this->process_product( $product ) ) {
					$output_count++;
				}
				// Quit if we've done all of the products
				if ( -1 !== $limit && $output_count >= $limit ) {
					$this->debug->log( '[%d] Reached limit (%d). Exiting.', [ __LINE__, $limit ] );
					break;
				}
			}
			if ( -1 !== $limit && $output_count >= $limit ) {
				$this->debug->log( 'Reached limit (%d). Exiting.', [ $limit ] );
				break;
			}
			$args['offset'] += $chunk_size;

			// If we're using the built-in object cache then flush it every chunk so
			// that we don't keep churning through memory.
			if ( ! $_wp_using_ext_object_cache ) {
				wp_cache_flush();
			}

			$products      = wc_get_products( $args );
			$product_count = count( $products );

			$this->debug->log( 'Retrieved %d products', [ $product_count ] );
		}
		$this->feed->render_footer();
	}


	/**
	 * Process a product, outputting its information.
	 *
	 * Uses process_simple_product() to process simple products, or all products if variation
	 * support is disabled. Uses process_variable_product() to process variable products.
	 *
	 * @param object $product Product ID / WC_Product / WP_Post
	 *
	 * @return bool                  True if one or more products were output,
	 *                               false otherwise.
	 */
	private function process_product( $product ) {

		// Make sure we have a WC_Product.
		if ( is_int( $product ) ) {
			$woocommerce_product = wc_get_product( $product );
		} elseif ( get_class( $product ) === 'WP_Post' ) {
			$woocommerce_product = wc_get_product( $product );
		} else {
			$woocommerce_product = $product;
		}
		// WC's product query can return IDs that don't resolve to actual products.
		if ( empty( $woocommerce_product ) ) {
			return false;
		}
		$product_type = $woocommerce_product->get_type();
		$this->debug->log( 'Processing %s product (%d)', [ $product_type, $woocommerce_product->get_id() ] );

		$include_variations = apply_filters(
			'woocommerce_gpf_include_variations',
			! empty( $this->settings['include_variations'] ),
			$woocommerce_product
		);
		if ( $woocommerce_product instanceof WC_Product_Variable &&
			 $include_variations ) {
			return $this->process_variable_product( $woocommerce_product );
		}

		return $this->process_simple_product( $woocommerce_product );
	}

	/**
	 * Process a simple product, and output its elements.
	 *
	 * @param object $woocommerce_product WooCommerce Product Object (May not be Simple)
	 *
	 * @return bool                          True if one or more products were output, false
	 *                                       otherwise.
	 */
	private function process_simple_product( $woocommerce_product ) {

		// Check whether it should be excluded
		if ( WoocommerceGpfFeedItem::should_exclude(
			$woocommerce_product,
			$this->feed_config->type
		) ) {
			$this->debug->log( '%d excluded, skipping...', [ $woocommerce_product->get_id() ] );
			$this->cache->store( $woocommerce_product->get_id(), $this->feed_config->type, '' );

			return false;
		}
		// Construct the data for this item.
		$feed_item = $this->feed_item_factory->create(
			$this->feed_config->type,
			$woocommerce_product,
			$woocommerce_product
		);

		$output = apply_filters(
			'woocommerce_gpf_render_item_output_' . $this->feed_config->type,
			$this->feed->render_item( $feed_item ),
			$feed_item,
			$woocommerce_product
		);
		$this->cache->store( $feed_item->ID, $this->feed_config->type, $output );
		echo $output;

		return ! empty( $output );
	}

	/**
	 * Process a variable product, and output its elements.
	 *
	 * @param object $woocommerce_product WooCommerce Product Object
	 *
	 * @return bool                          True if one or more products were output, false
	 *                                       otherwise.
	 */
	private function process_variable_product( $woocommerce_product ) {

		// Check if the whole product is excluded.
		if ( WoocommerceGpfFeedItem::should_exclude( $woocommerce_product, $this->feed_config->type ) ) {
			$this->cache->store( $woocommerce_product->get_id(), $this->feed_config->type, '' );
			$this->debug->log( '%d excluded, skipping...', [ $woocommerce_product->get_id() ] );

			return false;
		}
		$variation_ids = $woocommerce_product->get_children();
		$output        = '';
		foreach ( $variation_ids as $variation_id ) {
			// Get the variation product.
			$variation_product = wc_get_product( $variation_id );
			if ( ! $variation_product ) {
				continue;
			}
			$feed_item = $this->feed_item_factory->create(
				$this->feed_config->type,
				$variation_product,
				$woocommerce_product
			);

			// Skip to the next if this variation isn't to be included.
			if ( $feed_item->is_excluded() ) {
				$this->debug->log( 'variation %d is excluded', [ $feed_item->specific_id ] );
				continue;
			}

			// Render it.
			$output .= $this->feed->render_item( $feed_item );
		}
		$this->cache->store( $woocommerce_product->get_id(), $this->feed_config->type, $output );
		echo $output;

		return ! empty( $output );
	}
}
