<?php

class WoocommerceProductFeedsFeedItemFactory {

	/**
	 * @var WoocommerceGpfCommon
	 */
	protected $common;

	/**
	 * @var WoocommerceGpfDebugService
	 */
	protected $debug;

	/**
	 * @var WoocommerceProductFeedsTermDepthRepository
	 */
	protected $term_depth_repository;

	/**
	 * WoocommerceProductFeedsFeedItemFactory constructor.
	 *
	 * @param WoocommerceGpfCommon $common
	 * @param WoocommerceGpfDebugService $debug
	 * @param WoocommerceProductFeedsTermDepthRepository $term_depth_repository
	 */
	public function __construct(
		WoocommerceGpfCommon $common,
		WoocommerceGpfDebugService $debug,
		WoocommerceProductFeedsTermDepthRepository $term_depth_repository
	) {
		$this->common                = $common;
		$this->debug                 = $debug;
		$this->term_depth_repository = $term_depth_repository;
	}

	/**
	 * @param $feed_type
	 * @param $specific_product
	 * @param $general_product
	 * @param bool $calculate_prices
	 *
	 * @return WoocommerceGpfFeedItem
	 */
	public function create( $feed_type, $specific_product, $general_product, $calculate_prices = true ) {
		$feed_item = new WoocommerceGpfFeedItem(
			$specific_product,
			$general_product,
			$feed_type,
			$this->common,
			$this->debug,
			$this->term_depth_repository,
			$calculate_prices
		);
		$feed_item = apply_filters( 'woocommerce_gpf_feed_item', $feed_item, $specific_product );
		return apply_filters( 'woocommerce_gpf_feed_item_' . $feed_type, $feed_item, $specific_product );
	}
}
