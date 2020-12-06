<?php

class WoocommercePrfGoogleReviewProductInfo {
	/**
	 * @var WoocommerceGpfDebugService
	 */
	protected $debug;

	/**
	 * @var WoocommerceGpfCache
	 */
	private $cache;

	/**
	 * @var WoocommerceGpfCommon
	 */
	private $common;

	/**
	 * WoocommercePrfGoogleReviewProductInfo constructor.
	 *
	 * Instantiate a cache item.
	 *
	 * @param WoocommerceGpfCache $woocommerce_gpf_cache
	 * @param WoocommerceGpfCommon $woocommerce_gpf_common
	 * @param WoocommerceGpfDebugService $woocommerce_gpf_debug_service
	 */
	public function __construct(
		WoocommerceGpfCache $woocommerce_gpf_cache,
		WoocommerceGpfCommon $woocommerce_gpf_common,
		WoocommerceGpfDebugService $woocommerce_gpf_debug_service
	) {
		$this->cache  = $woocommerce_gpf_cache;
		$this->common = $woocommerce_gpf_common;
		$this->debug  = $woocommerce_gpf_debug_service;

		$this->common->initialise();

	}

	/**
	 * Rebuild the cache for an item.
	 *
	 * @param WC_Product $wc_product
	 *
	 * @return array
	 */
	public function rebuild_item( $wc_product ) {
		if ( is_null( $wc_product ) ) {
			return [];
		}
		if ( $wc_product->get_type() === 'variable' ) {
			$product_info = $this->get_product_info_variable( $wc_product );
			$this->cache->store( $wc_product->get_id(), 'googlereview', serialize( $product_info ) );

			return $product_info;
		}
		$product_info = $this->get_product_info_simple( $wc_product );
		$this->cache->store( $wc_product->get_id(), 'googlereview', serialize( $product_info ) );

		return $product_info;
	}

	/**
	 * Pull product identifiers based on Google Product Feed configuration.
	 *
	 * May retrieve results from the cache, or generate them.
	 *
	 * @param int $product_id The product ID to fetch information for.
	 *
	 * @return array               The product info array.
	 */
	public function get_product_info( $product_id ) {
		$cached_info = $this->cache->fetch( $product_id, 'googlereview' );
		if ( ! empty( $cached_info ) ) {
			return unserialize( $cached_info );
		}

		return $this->rebuild_item( wc_get_product( $product_id ) );
	}

	/**
	 * Generate product info for a simple product.
	 *
	 * @param $wc_product
	 *
	 * @return array
	 */
	protected function get_product_info_simple( $wc_product ) {

		if ( empty( $wc_product ) ) {
			return [];
		}
		if ( 'product_variation' === $wc_product->get_type() ) {
			$gpf_feed_item = new WoocommerceGpfFeedItem(
				$wc_product,
				wc_get_product( $wc_product->get_parent_id() ),
				'all',
				$this->common,
				$this->debug
			);
		} else {
			$gpf_feed_item = new WoocommerceGpfFeedItem(
				$wc_product,
				$wc_product,
				'all',
				$this->common,
				$this->debug
			);
		}
		if ( ! $gpf_feed_item ) {
			return [];
		}

		$product_info = [
			'gtins'  => [],
			'mpns'   => [],
			'brands' => [],
			'skus'   => [],
		];

		if ( ! empty( $gpf_feed_item->additional_elements['gtin'] ) ) {
			$product_info['gtins'] = isset( $gpf_feed_item->additional_elements['gtin'] ) ?
				$gpf_feed_item->additional_elements['gtin'] :
				[];
		}
		if ( ! empty( $gpf_feed_item->additional_elements['mpn'] ) ) {
			$product_info['mpns'] = isset( $gpf_feed_item->additional_elements['mpn'] ) ?
				$gpf_feed_item->additional_elements['mpn'] :
				[];
		}
		if ( ! empty( $gpf_feed_item->additional_elements['brand'] ) ) {
			$product_info['brands'] = isset( $gpf_feed_item->additional_elements['brand'] ) ?
				$gpf_feed_item->additional_elements['brand'] :
				[];
		}
		if ( ! empty( $gpf_feed_item->sku ) ) {
			$product_info['skus'] = isset( $gpf_feed_item->sku ) ?
				[ $gpf_feed_item->sku ] :
				[];
		}
		$product_info['skus'][] = 'woocommerce_gpf_' . $wc_product->get_id();

		return $product_info;
	}

	/**
	 * Generate product info for a variable product.
	 *
	 * @param $wc_product
	 *
	 * @return array
	 */
	protected function get_product_info_variable( $wc_product ) {
		$product_info = [
			'gtins'  => [],
			'mpns'   => [],
			'brands' => [],
			'skus'   => [],
		];

		$child_ids = $wc_product->get_children();
		foreach ( $child_ids as $child_id ) {
			$child_info           = $this->get_product_info_simple( wc_get_product( $child_id ) );
			$child_info['skus'][] = 'woocommerce_gpf_' . $child_id;
			$product_info         = array_merge_recursive( $product_info, $child_info );
			$product_info         = array_map( 'array_unique', $product_info );
		}

		$parent_info            = $this->get_product_info_simple( $wc_product );
		$product_info           = array_merge_recursive( $product_info, $parent_info );
		$product_info['skus'][] = 'woocommerce_gpf_' . $wc_product->get_id();
		$product_info           = array_map( 'array_unique', $product_info );

		return $product_info;
	}
}
