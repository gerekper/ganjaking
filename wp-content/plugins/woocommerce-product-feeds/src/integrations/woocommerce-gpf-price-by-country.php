<?php

class WoocommerceGpfPriceByCountry {

	/**
	 * @var string
	 */
	private $currency = '';

	/**
	 * Capture the currency requested. Add hooks / filters.
	 */
	public function run() {
		// Bail if no currency forced.
		if ( empty( $_GET['pricecountry'] ) ) {
			return;
		}
		$this->currency = $_GET['pricecountry'];
		add_filter( 'woocommerce_gpf_cache_name', array( $this, 'granularise_cache_name' ), 10, 1 );
		add_filter( 'woocommerce_gpf_feed_item', array( $this, 'add_currency_arg_to_product_permalinks' ), 10, 2 );
	}

	/**
	 * @param string $name
	 *
	 * @return string
	 */
	public function granularise_cache_name( $name ) {
		return $name . '_' . $this->currency;
	}

	/**
	 * @param $feed_item
	 * @param $wc_product
	 *
	 * @return mixed
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function add_currency_arg_to_product_permalinks( $feed_item, $wc_product ) {
		$feed_item->purchase_link = add_query_arg(
			array(
				'pricecountry' => $this->currency,
			),
			$feed_item->purchase_link
		);

		return $feed_item;
	}
}
