<?php

namespace WCML\Compatibility\WcCheckoutAddons;

class MulticurrencyHooks implements \IWPML_Action {

	public function add_hooks() {
		add_filter( 'option_wc_checkout_add_ons', [ $this, 'optionWcCheckoutAddOnsFilter' ] );
	}

	/**
	 * @param array|mixed $optionValue
	 *
	 * @return array|mixed
	 */
	public function optionWcCheckoutAddOnsFilter( $optionValue ) {
		$convertPrice = function( $index, $conf ) {
			if (
				isset( $conf['adjustment'], $conf['adjustment_type'] )
				&& 'fixed' === $conf['adjustment_type']
			) {
				$conf['adjustment'] = apply_filters( 'wcml_raw_price_amount', $conf['adjustment'] );
			}

			return $conf;
		};

		return OptionIterator::apply( $convertPrice, $optionValue );
	}
}
