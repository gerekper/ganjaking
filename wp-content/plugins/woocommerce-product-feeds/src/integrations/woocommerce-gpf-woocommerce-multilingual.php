<?php

class WoocommerceGpfWoocommerceMultilingual {

	/**
	 * @var string
	 */
	private $currency = '';

	/**
	 * Capture the currency requested. Add hooks / filters.
	 */
	public function run() {
		// Bail if no currency forced.
		if ( empty( $_REQUEST['currency'] ) ) {
			return;
		}

		$this->currency = strtoupper( $_REQUEST['currency'] );
		add_filter( 'wcml_client_currency', [ $this, 'set_currency' ], 10, 1 );
		add_filter( 'woocommerce_gpf_cache_name', [ $this, 'granularise_cache_name' ], 10, 1 );
		add_filter( 'woocommerce_gpf_feed_item', [ $this, 'add_currency_arg_to_product_permalinks' ], 10, 2 );
	}

	/**
	 * Set the desired currency in WooCommerce Multilingual
	 *
	 * @param $current_currency
	 *
	 * @return mixed
	 */
	public function set_currency( $current_currency ) {
		if ( is_admin() ) {
			return $current_currency;
		}
		WC()->session->set( 'client_currency', $this->currency );

		return $this->currency;
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
			[ 'currency' => $this->currency ],
			$feed_item->purchase_link
		);

		return $feed_item;
	}
}
