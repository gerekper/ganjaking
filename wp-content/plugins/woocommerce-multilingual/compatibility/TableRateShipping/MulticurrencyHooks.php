<?php

namespace WCML\Compatibility\TableRateShipping;

class MulticurrencyHooks implements \IWPML_Action {

	/**
	 * @var \woocommerce_wpml
	 */
	public $woocommerce_wpml;

	/**
	 * @var \WCML_Multi_Currency
	 */
	public $multicurrency;

	/**
	 * @param \woocommerce_wpml    $woocommerce_wpml
	 * @param \WCML_Multi_Currency $multicurrency
	 */
	public function __construct( \woocommerce_wpml $woocommerce_wpml, \WCML_Multi_Currency $multicurrency ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->multicurrency    = $multicurrency;
	}

	public function add_hooks() {
		if ( version_compare( constant( 'TABLE_RATE_SHIPPING_VERSION' ), '3.0.11', '<' ) ) {
			add_filter( 'woocommerce_table_rate_query_rates_args', [ $this, 'filterQueryRatesArgs' ] );
		}

		add_filter( 'woocommerce_table_rate_package_row_base_price', [ $this, 'filterProductBasePrice' ], 10, 3 );
	}

	/**
	 * It's not possible to filter rate_min and rate_max so we use the original price to compare against these values
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function filterQueryRatesArgs( $args ) {
		if ( isset( $args['price'] ) && wcml_get_woocommerce_currency_option() !== $this->multicurrency->get_client_currency() ) {
			$args['price'] = $this->multicurrency->prices->unconvert_price_amount( $args['price'] );
		}

		return $args;
	}

	/**
	 * @param float       $rowBasePrice
	 * @param \WC_Product $product
	 * @param int         $quantity
	 *
	 * @return float
	 */
	public function filterProductBasePrice( $rowBasePrice, $product, $quantity ) {
		if ( $product && wcml_get_woocommerce_currency_option() !== $this->multicurrency->get_client_currency() ) {
			/** @var mixed */
			$rowBasePrice = $this->woocommerce_wpml->products->get_product_price_from_db( $product->get_id() );
			$rowBasePrice *= $quantity;
		}

		return $rowBasePrice;
	}

}
