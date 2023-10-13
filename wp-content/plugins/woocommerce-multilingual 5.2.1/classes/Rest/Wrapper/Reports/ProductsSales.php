<?php

namespace WCML\Rest\Wrapper\Reports;

use WCML\Rest\Exceptions\InvalidLanguage;
use WCML\Rest\Wrapper\Handler;
use WPML\FP\Obj;

class ProductsSales extends Handler {

	/**
	 * Return currency information for products selles response.
	 *
	 * @param \WP_REST_Response $response
	 * @param object            $object
	 * @param \WP_REST_Request  $request
	 *
	 * @throws InvalidLanguage
	 *
	 * @return \WP_REST_Response
	 */
	public function prepare( $response, $object, $request ) {

		$currency = Obj::prop( 'currency', $request->get_params() );

		if ( $currency ) {

			$response->data['currency'] = $currency;
		}

		return $response;
	}

}