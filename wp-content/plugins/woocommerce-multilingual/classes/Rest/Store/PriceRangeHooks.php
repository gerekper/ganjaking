<?php

namespace WCML\Rest\Store;

use WCML\Rest\Functions;
use WPML\FP\Obj;

use function WPML\FP\partialRight;

class PriceRangeHooks implements \IWPML_Action {

	/**
	 * @var \woocommerce_wpml
	 */
	private $woocommerce_wpml;

	/**
	 * @param \woocommerce_wpml $woocommerce_wpml
	 */
	public function __construct( $woocommerce_wpml ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
	}

	public function add_hooks() {
		add_filter( 'rest_request_after_callbacks', [ $this, 'convertPriceRange' ], 10, 3 );
	}

	/**
	 * @param \WP_REST_Response $response
	 * @param array             $handler
	 * @param \WP_REST_Request  $request
	 *
	 * @return \WP_REST_Response
	 */
	public function convertPriceRange( $response, $handler, $request ) {
		if (
			\WP_REST_Server::READABLE === $request->get_method()
			&& 'products/collection-data' === Functions::getStoreStrippedEndpoint( $request )
			&& $request->get_param( 'calculate_price_range' )
		) {
			$mc = $this->woocommerce_wpml->multi_currency;

			$fromCurrency = $mc->get_default_currency();
			$toCurrency   = $mc->get_client_currency();
			if ( $fromCurrency !== $toCurrency ) {
				$data = $response->get_data();
				if ( ! empty( $data['price_range'] ) ) {
					/** @var callable(int|float): (int|float) $convert */
					$convert = partialRight( [ $mc->prices, 'convert_price_amount' ], $toCurrency );
					$data    = Obj::over( Obj::lensPath( [ 'price_range', 'min_price' ] ), $convert, $data );
					$data    = Obj::over( Obj::lensPath( [ 'price_range', 'max_price' ] ), $convert, $data );
					$response->set_data( $data );
				}

			}
		}

		return $response;
	}

}
