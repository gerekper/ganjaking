<?php

namespace WCML\Compatibility\StripePayments;

class MulticurrencyHooks implements \IWPML_Action {

	/**
	 * @var \WCML_Multi_Currency_Orders
	 */
	private $orders;

	const PRIORITY = 10;

	public function __construct( \WCML_Multi_Currency_Orders $orders ) {
		$this->orders = $orders;
	}

	public function add_hooks() {
		add_action( 'woocommerce_admin_order_totals_after_total', [ $this, 'suspendCurrencySymbolFilter' ], self::PRIORITY - 1 );
	}

	public function suspendCurrencySymbolFilter() {
		if ( remove_filter( 'woocommerce_currency_symbol', [ $this->orders, '_use_order_currency_symbol' ] ) ) {
			add_action(
				'woocommerce_admin_order_totals_after_total',
				function() {
					add_filter( 'woocommerce_currency_symbol', [ $this->orders, '_use_order_currency_symbol' ] );
				},
				self::PRIORITY + 100
			);
		}
	}
}
