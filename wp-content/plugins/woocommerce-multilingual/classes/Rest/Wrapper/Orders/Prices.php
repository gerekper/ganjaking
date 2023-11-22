<?php

namespace WCML\Rest\Wrapper\Orders;

use WCML\Orders\Helper as OrdersHelper;
use WPML\FP\Obj;
use WCML\Rest\Wrapper\Handler;
use WCML\Rest\Exceptions\InvalidCurrency;

class Prices extends Handler {

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

			OrdersHelper::setCurrency( $orderId, $currency );
		}
	}
}