<?php

namespace Premmerce\WooCommercePinterest\Frontend\Analytics\Event;

use \WC_Product;

class LeadEvent extends AbstractEvent implements EventInterface {


	/**
	 * Return event status
	 *
	 * @return bool
	 */
	public function enabled() {
		return $this->isEnabledInOptions();
	}

	/**
	 * Return if event was fired
	 *
	 * @return bool
	 */
	public function fired() {
		return is_product();
	}

	/**
	 * Return event name
	 *
	 * @return string
	 */
	public function getName() {
		return 'Lead';
	}

	/**
	 * Return data to be sent with analytics event
	 *
	 * @return array
	 */
	public function getData() {
		global $product;
		$data = array();
		if ($product instanceof WC_Product) {
			$data = $this->getProductData($product);
		}

		return $data;
	}

	/**
	 * Return product data
	 *
	 * @param WC_Product $product
	 */
	private function getProductData( WC_Product $product) {
		$data = array(
			'value'        => intval($product->get_price()),
			'currency'     => get_woocommerce_currency(),
			'product_name' => $product->get_name(),
			'product_id'   => $product->get_id(),
		);

		$category = $product->get_category_ids();

		if (! empty($category[0])) {//todo: use primary category
			$data['quantity'] = get_term_by('id', $category[0], 'product_cat')->name;
		}
	}

	/**
	 * Return deferred status
	 * If event is deferred, it will be saved to transients and fired on next request handling
	 *
	 * @return bool
	 */
	public function isDeferred() {
		return false;
	}
}
