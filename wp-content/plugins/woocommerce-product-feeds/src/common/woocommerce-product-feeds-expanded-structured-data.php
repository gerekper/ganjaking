<?php

/**
 * woocommerce_gpf_structured_data
 *
 * Enriches the on-page microdata based on Google Product Feed data values.
 */
class WoocommerceProductFeedsExpandedStructuredData {

	/**
	 * @var WoocommerceGpfCommon
	 */
	protected $common;

	/**
	 * @var WoocommerceGpfDebugService
	 */
	protected $debug;

	/**
	 * @var array
	 */
	protected $additional_product_markup = [];

	/**
	 * @var int
	 */
	private $cache_ttl;

	/**
	 * Constructor.
	 *
	 * Store dependencies.
	 *
	 * @param WoocommerceGpfCommon $woocommerce_gpf_common
	 * @param WoocommerceGpfDebugService $debug
	 */
	public function __construct(
		WoocommerceGpfCommon $woocommerce_gpf_common,
		WoocommerceGpfDebugService $debug
	) {
		$this->common = $woocommerce_gpf_common;
		$this->debug  = $debug;
		$this->seller = [
			'@type' => 'Organization',
			'name'  => get_bloginfo( 'name' ),
			'url'   => home_url(),
		];
	}

	/**
	 *    Add the filters so we can modify the structured data.
	 */
	public function initialise() {
		$this->cache_ttl = apply_filters( 'woocommerce_gpf_schema_cache_ttl', 604800 );
		add_filter( 'woocommerce_single_product_summary', [ $this, 'generate_schema_products' ] );
		add_filter( 'wp_footer', [ $this, 'output_markup' ] );
	}

	/**
	 * Generate Product schema record(s) for the product.
	 */
	public function generate_schema_products() {

		global $product;
		$wc_product = $product;

		// Check we have a valid product, and try and load from the cache if possible
		if ( ! is_a( $wc_product, 'WC_Product' ) ||
			 $this->load_schema_cache( $wc_product )
		) {
			return;
		}

		// If not, generate it.
		switch ( $wc_product->get_type() ) {
			case 'variable':
				$this->generate_variation_schema_products( $wc_product );
				break;
			case 'simple':
				$this->generate_simple_schema_product( $wc_product );
				break;
		}

		$this->save_schema_cache( $wc_product );
	}

	/**
	 * Generate a Product for each of the child products if the product is a variable product.
	 *
	 * @param WC_Product $wc_product
	 *
	 * @return void
	 */
	private function generate_variation_schema_products( $wc_product ) {

		// Retrieve the child variations and bail if we have too many to process.
		$variation_ids = $wc_product->get_children();
		if ( count( $variation_ids ) > apply_filters( 'woocommerce_gpf_max_variations_for_structured_data', 50 ) ) {
			return;
		}

		// Build a product for each variation.
		foreach ( $variation_ids as $variation_id ) {
			$wc_product_variation = wc_get_product( $variation_id );
			if ( ! $wc_product_variation ) {
				continue;
			}
			$this->generate_schema_product( $wc_product, $wc_product_variation );
		}
	}

	/**
	 * @param WC_Product $wc_product
	 */
	private function generate_simple_schema_product( $wc_product ) {
		$this->generate_schema_product( $wc_product, $wc_product );
	}

	/**
	 * Generate a schema Product record for a single product, and add it to the list.
	 *
	 * @param WC_Product $wc_product_generic
	 * @param WC_Product $wc_product_specific
	 */
	private function generate_schema_product( $wc_product_generic, $wc_product_specific ) {
		// Get the feed information for this product.
		$feed_item = new WoocommerceGpfFeedItem(
			$wc_product_specific,
			$wc_product_generic,
			'google',
			$this->common,
			$this->debug,
			true
		);

		// Create the basic Product shell.
		$markup = [
			'@type'       => 'Product',
			'@id'         => $feed_item->purchase_link . '#gpf-product',
			'name'        => $feed_item->title,
			'description' => $feed_item->description,
			'url'         => $feed_item->purchase_link,
		];

		// For variable products, group the various products together.
		if ( $wc_product_specific->get_type() === 'variation' ) {
			$markup['inProductGroupWithID'] = $feed_item->item_group_id;
		}

		// Image.
		if ( ! empty( $feed_item->image_link ) ) {
			$markup['image'] = $feed_item->image_link;
		}

		// Brand.
		if ( isset( $feed_item->additional_elements['brand'][0] ) ) {
			$markup['brand'] = $feed_item->additional_elements['brand'][0];
		}

		// Condition.
		if ( isset( $feed_item->additional_elements['condition'][0] ) ) {
			$markup['itemCondition'] = $this->schemaize_condition( $feed_item->additional_elements['condition'][0] );
		}

		// GTIN.
		if ( isset( $feed_item->additional_elements['gtin'][0] ) ) {
			$gtin_length = strlen( $feed_item->additional_elements['gtin'][0] );
			$key         = 'gtin' . $gtin_length;
			switch ( $gtin_length ) {
				case 8:
				case 12:
				case 13:
				case 14:
					$markup[ $key ] = $feed_item->additional_elements['gtin'][0];
					break;
			}
		}

		// MPN.
		if ( isset( $feed_item->additional_elements['mpn'] ) ) {
			$markup['mpn'] = $feed_item->additional_elements['mpn'][0];
		}

		// SKU.
		$markup['sku'] = $feed_item->guid;
		if ( ! empty( $feed_item->sku ) ) {
			$markup['sku'] = $feed_item->sku;
		}

		// Calculate the priceValidUntil date for the variation.
		$price_valid_until = gmdate( 'c', time() + ( 30 * DAY_IN_SECONDS ) );
		if ( $wc_product_specific->is_on_sale() && $wc_product_specific->get_date_on_sale_to() ) {
			$price_valid_until = gmdate( 'c', $wc_product_specific->get_date_on_sale_to()->getTimestamp() );
		}

		// Add priceSpecification.
		if ( wc_tax_enabled() ) {
			$price_specifications = [
				[
					'price'                 => number_format( $feed_item->price_inc_tax, 2, '.', '' ),
					'priceCurrency'         => get_woocommerce_currency(),
					'valueAddedTaxIncluded' => true,
				],
				[
					'price'                 => number_format( $feed_item->price_ex_tax, 2, '.', '' ),
					'priceCurrency'         => get_woocommerce_currency(),
					'valueAddedTaxIncluded' => false,
				],
			];
		} else {
			$price_specifications = [
				[
					'price'         => $feed_item->price_inc_tax,
					'priceCurrency' => get_woocommerce_currency(),
				],
			];
		}

		// Offer
		$markup['offers'] = [
			[
				'@type'              => 'Offer',
				'url'                => $feed_item->purchase_link,
				'priceCurrency'      => get_woocommerce_currency(),
				'priceSpecification' => $price_specifications,
				'priceValidUntil'    => $price_valid_until,
				'seller'             => $this->seller,
			],
		];

		$aggregate_rating = $this->generate_aggregate_rating( $wc_product_generic );
		if ( ! empty( $aggregate_rating ) ) {
			$markup['aggregateRating'] = $aggregate_rating;
		}

		$review = $this->generate_review( $wc_product_generic );
		if ( ! empty( $review ) ) {
			$markup['review'] = $review;
		}

		// Add availability to the offer.
		if ( isset( $feed_item->additional_elements['availability'][0] ) ) {
			$markup['offers'][0]['availability'] = $this->schemaize_availability( $feed_item->additional_elements['availability'][0] );
		}

		$this->additional_product_markup[] = apply_filters(
			'woocommerce_gpf_variation_product_schema',
			$markup,
			$wc_product_generic,
			$wc_product_specific
		);
	}

	/**
	 * Output the schema markup.
	 */
	public function output_markup() {
		$json_args = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ?
			JSON_PRETTY_PRINT :
			0;
		if ( is_product() && ! empty( $this->additional_product_markup ) ) {
			echo '<script id="woocommerce_gpf_schema" type="application/ld+json">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo wc_esc_json(
				wp_json_encode(
					[
						'@context' => 'https://schema.org/',
						'@graph'   => $this->additional_product_markup,
					],
					$json_args
				),
				true
			); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Map internal strings to Schema.org definitions for condition.
	 *
	 * @param $condition
	 *
	 * @return string
	 */
	private function schemaize_condition( $condition ) {
		switch ( strtolower( $condition ) ) {
			case 'new':
				return 'https://schema.org/NewCondition';
				break;
			case 'used':
				return 'https://schema.org/UsedCondition';
				break;
			case 'refurbished':
				return 'https://schema.org/RefurbishedCondition';
				break;
		}

		return $condition;
	}

	/**
	 * Map internal strings to Schema.org definitions for availability.
	 *
	 * @param $availability
	 *
	 * @return string
	 */
	private function schemaize_availability( $availability ) {
		switch ( strtolower( $availability ) ) {
			case 'in stock':
			case 'available for order':
				return 'https://schema.org/InStock';
				break;
			case 'out of stock':
				return 'https://schema.org/OutOfStock';
				break;
			case 'preorder':
				return 'https://schema.org/PreOrder';
				break;
		}

		return $availability;
	}

	/**
	 * Generate an agrregateRating data array for a product.
	 *
	 * Taken from WC_Structured_Data
	 *
	 * @param \WC_Product $product
	 */
	private function generate_aggregate_rating( \WC_Product $product ) {
		$aggregate_rating = [];
		if ( ! $product->get_rating_count() || ! wc_review_ratings_enabled() ) {
			return $aggregate_rating;
		}
		$aggregate_rating = [
			'@type'       => 'AggregateRating',
			'ratingValue' => $product->get_average_rating(),
			'reviewCount' => $product->get_review_count(),
		];

		return $aggregate_rating;
	}

	/**
	 * @param WC_Product $product
	 *
	 * Taken from WC_Structured_Data
	 */
	private function generate_review( WC_Product $product ) {

		$review = [];

		if ( ! $product->get_rating_count() || ! wc_review_ratings_enabled() ) {
			return $review;
		}

		// Markup 5 most recent rating/review.
		$comments = get_comments(
			[
				'number'      => 5,
				'post_id'     => $product->get_id(),
				'status'      => 'approve',
				'post_status' => 'publish',
				'post_type'   => 'product',
				'parent'      => 0,
				'meta_query'  => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					[
						'key'     => 'rating',
						'type'    => 'NUMERIC',
						'compare' => '>',
						'value'   => 0,
					],
				],
			]
		);

		if ( ! $comments ) {
			return $review;
		}

		foreach ( $comments as $comment ) {
			$review[] = [
				'@type'         => 'Review',
				'reviewRating'  => [
					'@type'       => 'Rating',
					'bestRating'  => '5',
					'ratingValue' => get_comment_meta( $comment->comment_ID, 'rating', true ),
					'worstRating' => '1',
				],
				'author'        => [
					'@type' => 'Person',
					'name'  => get_comment_author( $comment ),
				],
				'reviewBody'    => get_comment_text( $comment ),
				'datePublished' => get_comment_date( 'c', $comment ),
			];
		}

		return $review;
	}

	/**
	 * Attempt to load schema from cache.
	 *
	 * Return true if loaded, and not expired, false otherwise.
	 *
	 * @param $wc_product
	 *
	 * @return bool
	 */
	private function load_schema_cache( $wc_product ) {
		$schema_cache        = $wc_product->get_meta( 'woocommerce_gpf_schema_cache', true );
		$schema_cache_ts     = $wc_product->get_meta( 'woocommerce_gpf_schema_cache_timestamp', true );
		$schema_cache_expiry = (int) $schema_cache_ts + $this->cache_ttl;
		$min_ts_validity     = get_option( 'woocommerce_gpf_schema_min_timestamp_validity' );

		/**
		 * If:
		 * - we have a value
		 * - it is due to expire in the future
		 * - it is newer than the min cache validity timestamp
		 */
		if (
			! empty( $schema_cache ) &&
			$schema_cache_expiry > time() &&
			$schema_cache_ts > $min_ts_validity
		) {
			// Load from the cache, and indicate that we have done so.
			$this->additional_product_markup = $schema_cache;

			return true;
		}

		// Did not find it.
		return false;
	}

	/**
	 * Cache the generated values for the product.
	 *
	 * @param $wc_product
	 */
	private function save_schema_cache( $wc_product ) {
		$wc_product->update_meta_data( 'woocommerce_gpf_schema_cache', $this->additional_product_markup );
		$wc_product->update_meta_data( 'woocommerce_gpf_schema_cache_timestamp', time() );
		$wc_product->save_meta_data();
	}
}
