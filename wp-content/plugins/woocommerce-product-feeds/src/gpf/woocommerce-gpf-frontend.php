<?php
/**
 * Frontend class.
 *
 * Handles grabbing the products and invoking the relevant feed class to render the feed.
 */
class WoocommerceGpfFrontend {

	protected $feed        = null;
	protected $feed_format = '';
	protected $settings    = array();

	/**
	 * WC_Product_Factory instance.
	 *
	 * @var WC_Product_Factory
	 */
	private $factory;

	/**
	 * Constructor. Grab the settings, and add filters if we have stuff to do
	 *
	 * @access public
	 */
	public function __construct() {

		global $wp_query, $woocommerce_gpf_common;

		$all_feed_types = $woocommerce_gpf_common->get_feed_types();
		$feed_type      = isset( $wp_query->query_vars['woocommerce_gpf'] ) ? $wp_query->query_vars['woocommerce_gpf'] : '';
		if ( empty( $feed_type ) ) {
			return;
		}

        // If we get here then this is a feed request. Check WC version, bail if incompatible.
        if ( !is_callable('wc') || version_compare( wc()->version, '3.0', 'lt' ) ) {
            return;
        }
        // Load the settings and check we have a valid feed type. Bail if not.
		$this->settings = get_option( 'woocommerce_gpf_config', array() );
		if ( ! empty( $all_feed_types[ $feed_type ] ) && ! empty( $this->settings['gpf_enabled_feeds'][ $feed_type ] ) ) {
			$class             = $all_feed_types[ $feed_type ]['class'];
			$this->feed        = new $class();
			$this->feed_format = $feed_type;
			add_action( 'template_redirect', array( $this, 'render_product_feed' ), 15 );
		} else {
			die( __( 'Invalid feed requested', 'woocommerce_gpf' ) );
		}
		// Add category filters if requested. 
		if ( ! empty( $wp_query->query_vars['gpf_categories'] ) ) {
			add_filter( 'woocommerce_gpf_wc_get_products_args', array( $this, 'limit_categories' ) );
			add_filter( 'woocommerce_gpf_get_posts_args', array( $this, 'limit_categories' ) );
		}
	}

	public function limit_categories( $args ) {
		global $wp_query;
		$categories = explode( ',', $wp_query->query_vars['gpf_categories'] );
		$categories = array_map( 'intval', $categories );
		if ( 'woocommerce_gpf_get_posts_args' === current_action() ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'product_cat',
					'terms'    => $categories,
				),
			);
		} else {
			// Map the term IDs to slugs.
			$slugs = array();
			foreach ( $categories as $term_id ) {
				$term = get_term( $term_id );
				if ( ! is_wp_error( $term ) ) {
					$slugs[] = $term->slug;
				}
			}
			$args['category'] = $slugs;
		}
		return $args;
	}

	/**
	 * Set a number of optimisations to make sure the plugin is usable on lower end setups.
	 *
	 * We stop plugins trying to cache, or compress the output since that causes everything to be
	 * held in memory and causes memory issues.
	 */
	private function set_optimisations() {

		global $wpdb;

		// Don't cache feed under WP Super-Cache.
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
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
	 * @param  int    $chunk_size  The number of products to be retrieved per
	 *                             query.
	 *
	 * @return array               Array containing the query function name at
	 *                             index 0, and the arguments array at index 1.
	 */
	private function get_query_args( $chunk_size ) {
		global $wp_query;

		$offset = isset( $wp_query->query_vars['gpf_start'] ) ?
				  (int) $wp_query->query_vars['gpf_start'] :
				  0;
		$args = array(
			'status'  => array( 'publish' ),
			'type'    => array( 'simple', 'variable', 'composite', 'bundle' ),
			'limit'   => $chunk_size,
			'offset'  => $offset,
			'orderby' => 'ID',
			'order'   => 'ASC',
		);
		if ( $this->cache->is_enabled() ) {
			$args['return'] = 'ids';
		}
		return array(
			'wc_get_products',
			apply_filters(
				'woocommerce_gpf_wc_get_products_args',
				$args,
				'feed'
			),
		);
	}

	/**
	 * Render the product feed requests - calls the sub-classes according
	 * to the feed required.
	 *
	 * @access public
	 */
	public function render_product_feed() {

		global $wp_query, $_wp_using_ext_object_cache;

		$this->cache   = new WoocommerceGpfCache();
		$this->factory = new WC_Product_Factory();

		$this->set_optimisations();
		$this->feed->render_header();

		if ( $this->cache->is_enabled() ) {
			$chunk_size = 100;
		} else {
			$chunk_size = 10;
		}
		$chunk_size = apply_filters( 'woocommerce_gpf_chunk_size', $chunk_size, $this->cache->is_enabled() );

		list($query_function, $args) = $this->get_query_args( $chunk_size );

		$gpf_limit = isset( $wp_query->query_vars['gpf_limit'] ) ?
					 (int) $wp_query->query_vars['gpf_limit'] :
					 false;

		$output_count = 0;

		// Query for the products, and process them.
		// Note: $products will be:
		// - post IDs if the cache is enabled
		// - WC_Product objects if cache is disabled, and WC3+ in use
		// - WP_Post objects if < WC3.
		$products = $query_function( $args );

		while ( count( $products ) ) {

			if ( $this->cache->is_enabled() ) {
				// Output any that we have in the cache.
				$outputs = $this->cache->fetch_multi( $products, $this->feed_format );
				foreach ( $products as $product_id ) {
					if ( ! empty( $outputs[ $product_id ] ) ) {
						echo $outputs[ $product_id ];
						$output_count ++;
					}
					if ( $gpf_limit && $output_count >= $gpf_limit ) {
						break;
					}
				}
				// Remove any we got from the list to be generated.
				$products = array_diff( $products, array_keys( $outputs ) );
			}

			// Bail if we're done.
			if ( $gpf_limit && $output_count >= $gpf_limit ) {
				break;
			}

			// If we have any still to generate, go do them.
			foreach ( $products as $product ) {
				if ( $this->process_product( $product ) ) {
					$output_count++;
				}
				// Quit if we've done all of the products
				if ( $gpf_limit && $output_count >= $gpf_limit ) {
					break;
				}
			}
			if ( $gpf_limit && $output_count >= $gpf_limit ) {
				break;
			}
			$args['offset'] += $chunk_size;

			// If we're using the built in object cache then flush it every chunk so
			// that we don't keep churning through memory.
			if ( ! $_wp_using_ext_object_cache ) {
				wp_cache_flush();
			}

			$products = $query_function( $args );
		}
		$this->feed->render_footer();
	}


	/**
	 * Process a product, outputting its information.
	 *
	 * Uses process_simple_product() to process simple products, or all products if variation
	 * support is disabled. Uses process_variable_product() to process variable products.
	 *
	 * @param  object  $product      Product ID / WC_Product / WP_Post
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
		$product_type = $woocommerce_product->get_type();
		switch ( $product_type ) {
			case 'simple':
				return $this->process_simple_product( $woocommerce_product );
				break;
			case 'variable':
				if (
					apply_filters(
						'woocommerce_gpf_include_variations',
						! empty( $this->settings['include_variations'] ),
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
				// Unknown product type. Try and process as a simple product.
				return $this->process_simple_product( $woocommerce_product );
				break;
		}
	}

	/**
	 * Process a simple product, and output its elements.
	 *
	 * @param  object  $woocommerce_product  WooCommerce Product Object (May not be Simple)
	 * @return bool                          True if one or more products were output, false
	 *                                       otherwise.
	 */
	private function process_simple_product( $woocommerce_product ) {

		global $woocommerce_gpf_common;

		// Check whether it should be excluded
		if ( WoocommerceGpfFeedItem::should_exclude( $woocommerce_product, $this->feed_format, $woocommerce_gpf_common ) ) {
			$this->cache->store( $woocommerce_product->get_id(), $this->feed_format, '' );
			return false;
		}
		// Construct the data for this item.
		$feed_item = new WoocommerceGpfFeedItem( $woocommerce_product, $woocommerce_product, $this->feed_format, $woocommerce_gpf_common );

		// Allow other plugins to modify the item before its rendered to the feed
		$feed_item = apply_filters( 'woocommerce_gpf_feed_item', $feed_item, $woocommerce_product );
		$feed_item = apply_filters( 'woocommerce_gpf_feed_item_' . $this->feed_format, $feed_item, $woocommerce_product );

		$output = apply_filters(
			'woocommerce_gpf_render_item_output_' . $this->feed_format,
			$this->feed->render_item( $feed_item ),
			$feed_item,
			$woocommerce_product
		);
		$this->cache->store( $feed_item->ID, $this->feed_format, $output );
		echo $output;
		return ! empty( $output );
	}

	/**
	 * Process a variable product, and output its elements.
	 *
	 * @param  object  $woocommerce_product  WooCommerce Product Object
	 *
	 * @return bool                          True if one or more products were output, false
	 *                                       otherwise.
	 */
	private function process_variable_product( $woocommerce_product ) {

		global $woocommerce_gpf_common;

		// Check if the whole product is excluded.
		if ( WoocommerceGpfFeedItem::should_exclude( $woocommerce_product, $this->feed_format ) ) {
			$this->cache->store( $woocommerce_product->get_id(), $this->feed_format, '' );
			return false;
		}
		$variations = $woocommerce_product->get_available_variations();
		$output     = '';
		foreach ( $variations as $variation ) {
			// Get the variation product.
			$variation_id      = $variation['variation_id'];
			$variation_product = $this->factory->get_product( $variation_id );
			$feed_item         = new WoocommerceGpfFeedItem( $variation_product, $woocommerce_product, $this->feed_format, $woocommerce_gpf_common );

			// Skip to the next if this variation isn't to be included.
			if ( $feed_item->is_excluded() ) {
				continue;
			}

			// Allow other plugins to modify the item before its rendered to the feed
			$feed_item = apply_filters( 'woocommerce_gpf_feed_item', $feed_item, $variation_product );
			$feed_item = apply_filters( 'woocommerce_gpf_feed_item_' . $this->feed_format, $feed_item, $variation_product );

			// Render it.
			$output .= $this->feed->render_item( $feed_item );
		}
		$this->cache->store( $woocommerce_product->get_id(), $this->feed_format, $output );
		echo $output;
		return ! empty( $output );
	}

	/**
	 * Process a composite product.
	 *
	 * @param  object  $woocommerce_product  WooCommerce Product Object
	 * @return bool                          True if one or more products were output, false
	 *                                       otherwise.
	 */
	private function process_composite_product( $woocommerce_product ) {
		return $this->process_simple_product( $woocommerce_product );
	}

	/**
	 * Process a bundle product.
	 *
	 * @param  object  $woocommerce_product  WooCommerce Product Object
	 * @return bool                          True if one or more products were output, false
	 *                                       otherwise.
	 */
	private function process_bundle_product( $woocommerce_product ) {
		return $this->process_simple_product( $woocommerce_product );
	}
}
