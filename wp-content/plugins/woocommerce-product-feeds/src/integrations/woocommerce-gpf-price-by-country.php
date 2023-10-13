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
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$pricecountry = isset( $_GET['pricecountry'] ) ?
			sanitize_text_field( $_GET['pricecountry'] ) :
			'';
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		// Bail if no currency forced.
		if ( empty( $pricecountry ) ) {
			return;
		}
		$this->currency = $pricecountry;
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
	// phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	public function add_currency_arg_to_product_permalinks( $feed_item, $wc_product ) {
		$feed_item->purchase_link = add_query_arg(
			array(
				'pricecountry' => $this->currency,
			),
			$feed_item->purchase_link
		);

		return $feed_item;
	}
	// phpcs:enable Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
}
