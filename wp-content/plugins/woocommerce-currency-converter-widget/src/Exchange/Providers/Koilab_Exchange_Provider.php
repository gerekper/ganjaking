<?php
/**
 * KoiLab Exchange provider.
 *
 * @since 2.1.0
 */

namespace KoiLab\WC_Currency_Converter\Exchange\Providers;

use KoiLab\WC_Currency_Converter\Logger;
use WP_Error;

defined( 'ABSPATH' ) || exit;

/**
 * KoiLab Exchange Provider class.
 */
class Koilab_Exchange_Provider extends Base_Exchange_Provider implements Exchange_Provider {

	/**
	 * Base URL to make HTTP requests.
	 *
	 * @var string
	 */
	protected $base_url = 'https://ccw-rates.koilab.com/';

	/**
	 * The constructor.
	 *
	 * @since 2.1.0
	 */
	public function __construct() {
		$this->id          = 'koilab_exchange';
		$this->name        = 'KoiLab Exchange Rates';
		$this->privacy_url = 'https://woo.com/document/currency-converter-widget/privacy-policy/';
	}

	/**
	 * Gets the exchange rates.
	 *
	 * @since 2.1.0
	 *
	 * @param array $args Optional. Additional arguments. Default empty.
	 * @return array
	 */
	public function get_rates( array $args = array() ): array {
		$rates    = array();
		$response = $this->request( '', $args );

		if ( ! is_wp_error( $response ) && isset( $response['rates'] ) ) {
			$rates = $response['rates'];
		}

		return $rates;
	}

	/**
	 * Makes a request to the specified endpoint.
	 *
	 * @since 2.1.0
	 *
	 * @param string $endpoint The request endpoint.
	 * @param array  $args     Optional. The request arguments. Default empty.
	 * @param string $method   Optional. The request method. Default 'GET'.
	 * @return mixed The request response. WP_Error on failure.
	 */
	protected function request( string $endpoint, array $args = array(), string $method = 'GET' ) {
		$url = $this->base_url . wp_unslash( $endpoint );

		return $this->trigger_request(
			$url,
			array(
				'method'  => $method,
				'body'    => $args,
				'headers' => array(
					'Content-Type' => 'application/json',
				),
			)
		);
	}
}
