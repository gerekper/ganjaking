<?php


class WoocommercePrfGoogleReviewProductInfo {

	/**
	 * @var WoocommerceGpfCache
	 */
	private $cache;

	/**
	 * WoocommercePrfGoogleReviewProductInfo constructor.
	 *
	 * Instantiate a cache item.
	 */
	public function __construct() {
		$this->cache = new WoocommerceGpfCache();
	}

	/**
	 * Rebuild the cache for an item.
	 *
	 * @param int $product_id
	 * @param WC_Product $wc_product
	 *
	 * @return array
	 */
	public function rebuild_item( $product_id, $wc_product ) {
		if ( is_null( $wc_product ) ) {
			$wc_product = wc_get_product( $product_id );
		}
		if ( $wc_product->get_type() === 'variable' ) {
			$product_info = $this->get_product_info_variable( $product_id );
			$this->cache->store( $product_id, 'googlereview', serialize( $product_info ) );

			return $product_info;
		}
		$product_info = $this->get_product_info_simple( $product_id );
		$this->cache->store( $product_id, 'googlereview', serialize( $product_info ) );

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

		return $this->rebuild_item( $product_id, null );
	}

	/**
	 * Generate product info for a simple product.
	 *
	 * @param $product_id
	 *
	 * @return array
	 */
	protected function get_product_info_simple( $product_id ) {
		$post          = get_post( $product_id );
		$gpf_feed_item = woocommerce_gpf_get_feed_item( $post );
		$product_info  = array(
			'gtins'  => array(),
			'mpns'   => array(),
			'brands' => array(),
			'skus'   => array(),
		);

		if ( ! empty( $gpf_feed_item->additional_elements['gtin'] ) ) {
			$product_info['gtins'] = isset( $gpf_feed_item->additional_elements['gtin'] ) ?
				$gpf_feed_item->additional_elements['gtin'] :
				array();
		}
		if ( ! empty( $gpf_feed_item->additional_elements['mpn'] ) ) {
			$product_info['mpns'] = isset( $gpf_feed_item->additional_elements['mpn'] ) ?
				$gpf_feed_item->additional_elements['mpn'] :
				array();
		}
		if ( ! empty( $gpf_feed_item->additional_elements['brand'] ) ) {
			$product_info['brands'] = isset( $gpf_feed_item->additional_elements['brand'] ) ?
				$gpf_feed_item->additional_elements['brand'] :
				array();
		}
		if ( ! empty( $gpf_feed_item->sku ) ) {
			$product_info['skus'] = isset( $gpf_feed_item->sku ) ?
				array( $gpf_feed_item->sku ) :
				array();
		}
		$product_info['skus'][] = 'woocommerce_gpf_' . $product_id;
		return $product_info;
	}

	/**
	 * Generate product info for a variable product.
	 *
	 * @param $product_id
	 *
	 * @return array
	 */
	protected function get_product_info_variable( $product_id ) {
		$product_info = array(
			'gtins'  => array(),
			'mpns'   => array(),
			'brands' => array(),
			'skus'   => array(),
		);

		$product   = wc_get_product( $product_id );
		$child_ids = $product->get_children();
		foreach ( $child_ids as $child_id ) {
			$child_info   = $this->get_product_info_simple( $child_id );
			$child_info['skus'][] = 'woocommerce_gpf_' . $child_id;
			$product_info = array_merge_recursive( $product_info, $child_info );
			$product_info = array_map( 'array_unique', $product_info );
		}

		$parent_info  = $this->get_product_info_simple( $product_id );
		$product_info = array_merge_recursive( $product_info, $parent_info );
		$product_info['skus'][] = 'woocommerce_gpf_' . $product_id;
		$product_info = array_map( 'array_unique', $product_info );

		return $product_info;
	}
}
