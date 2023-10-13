<?php

namespace WCML\Compatibility\GravityForms;

class MulticurrencyHooks implements \IWPML_Action {

	public function add_hooks() {
		add_filter( 'gform_formatted_money', [ $this, 'wcml_convert_price' ], 10, 2 );
		add_filter( 'wcml_multi_currency_ajax_actions', [ $this, 'add_ajax_action' ] );
	}

	/**
	 * @param string $formatted
	 * @param string $unformatted
	 *
	 * @return string
	 */
	public function wcml_convert_price( $formatted, $unformatted ) {
		if ( ! is_admin() ) {
			$currency  = apply_filters( 'wcml_price_currency', wcml_get_woocommerce_currency_option() );
			$formatted = strip_tags( wc_price( apply_filters( 'wcml_raw_price_amount', $unformatted ), [ 'currency' => $currency ] ) );
		}
		return $formatted;
	}

	/**
	 * @param string[] $actions
	 *
	 * @return string[]
	 */
	public function add_ajax_action( $actions ) {
		$actions[] = 'get_updated_price'; // Deprecated from 2.7.
		$actions[] = 'gforms_get_updated_price';
		return $actions;
	}

}
