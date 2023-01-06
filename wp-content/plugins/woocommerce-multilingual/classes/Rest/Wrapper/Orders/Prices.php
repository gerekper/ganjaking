<?php

namespace WCML\Rest\Wrapper\Orders;

use WPML\FP\Obj;
use WCML\Rest\Wrapper\Handler;
use WCML\Rest\Exceptions\InvalidCurrency;

class Prices extends Handler {

	/** @var \WCML_Multi_Currency_Orders */
	private $wcmlMultiCurrencyOrders;

	public function __construct(
		\WCML_Multi_Currency_Orders $wcmlMultiCurrencyOrders
	) {
		$this->wcmlMultiCurrencyOrders = $wcmlMultiCurrencyOrders;
	}

	/**
	 * Sets the product information according to the provided language
	 *
	 * @param object           $object
	 * @param \WP_REST_Request $request
	 * @param bool             $creating
	 *
	 * @throws InvalidCurrency
	 *
	 */
	public function insert( $object, $request, $creating ) {

		$currency = Obj::prop( 'currency', $request->get_params() );

		if ( $currency ) {

			$orderId = $object->get_id();
			$currencies = get_woocommerce_currencies();

			if ( ! isset( $currencies[ $currency ] ) ) {
				throw new InvalidCurrency( $currency );
			}

			update_post_meta( $orderId, '_order_currency', $currency );
		}
	}
}