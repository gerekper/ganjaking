<?php

namespace WCML\Compatibility\WcPip;

class MulticurrencyHooks implements \IWPML_Action {

	public function add_hooks() {
		add_filter( 'woocommerce_currency_symbol', [ $this, 'filter_pip_currency_symbol' ] );
		add_filter( 'wcml_filter_currency_position', [ $this, 'filter_pip_currency_position' ] );
	}

	/**
	 * @param string $currencySymbol
	 *
	 * @return string
	 */
	public function filter_pip_currency_symbol( $currencySymbol ) {
		remove_filter( 'woocommerce_currency_symbol', [ $this, 'filter_pip_currency_symbol' ] );

		$currency = self::getPipOrderCurrency();

		if ( $currency ) {
			$currencySymbol = get_woocommerce_currency_symbol( $currency );
		}

		add_filter( 'woocommerce_currency_symbol', [ $this, 'filter_pip_currency_symbol' ] );

		return $currencySymbol;
	}

	/**
	 * @param string $currency
	 *
	 * @return string
	 */
	public function filter_pip_currency_position( $currency ) {
		remove_filter( 'wcml_filter_currency_position', [ $this, 'filter_pip_currency_position' ] );

		$currency = self::getPipOrderCurrency( $currency );

		add_filter( 'wcml_filter_currency_position', [ $this, 'filter_pip_currency_position' ] );

		return $currency;
	}

	/**
	 * @param string|false $currency
	 *
	 * @return string|false
	 */
	private static function getPipOrderCurrency( $currency = false ) {
		$pip_order_id = Helper::getPipOrderId();

		if ( $pip_order_id && isset( WC()->order_factory ) ) {

			$the_order = WC()->order_factory->get_order( $pip_order_id );

			if ( $the_order ) {
				$currency = $the_order->get_currency();

				if ( ! $currency && isset( $_COOKIE['_wcml_order_currency'] ) ) {
					$currency = $_COOKIE['_wcml_order_currency'];
				}
			}
		}

		return $currency;
	}
}
