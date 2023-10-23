<?php
/**
 * Open Exchange Rates provider.
 *
 * @since 2.1.0
 */

namespace KoiLab\WC_Currency_Converter\Exchange\Providers;

defined( 'ABSPATH' ) || exit;

use KoiLab\WC_Currency_Converter\Logger;
use WP_Error;

/**
 * Open Exchange Rates Provider class.
 */
class Open_Exchange_Provider extends Base_Exchange_Provider implements Exchange_Provider {

	/**
	 * Base URL to make HTTP requests.
	 *
	 * @var string
	 */
	protected $base_url = 'https://openexchangerates.org/api/';

	/**
	 * App ID.
	 *
	 * @var string
	 */
	protected $app_id;

	/**
	 * The constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param string $app_id APP ID.
	 */
	public function __construct( string $app_id ) {
		$this->id          = 'open_exchange';
		$this->name        = 'Open Exchange Rates';
		$this->privacy_url = 'https://openexchangerates.org/privacy';

		$this->app_id = $app_id;
	}

	/**
	 * Validates the given API credentials.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function validate_credentials(): bool {
		$response = $this->get_latest();

		return ( ! is_wp_error( $response ) );
	}

	/**
	 * Gets the latest rates.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args Optional. The request arguments. Default empty.
	 * @return array|WP_Error The request response. WP_Error on failure.
	 */
	public function get_latest( array $args = array() ) {
		return $this->request( 'latest.json', $args );
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
		$response = $this->get_latest( $args );

		if ( ! is_wp_error( $response ) && isset( $response['rates'] ) ) {
			$rates = $response['rates'];
		}

		return $rates;
	}

	/**
	 * Gets the rates refresh period in hours.
	 *
	 * @since 2.1.0
	 *
	 * @return int
	 */
	public function get_refresh_period(): int {
		$period = (int) get_option( 'wc_currency_converter_rates_refresh_period', 12 );

		if ( ! $period ) {
			$period = 12;
		}

		return $period;
	}

	/**
	 * Makes a request to the specified endpoint.
	 *
	 * @since 2.0.0
	 * @since 2.1.0 Added `$method` argument.
	 *
	 * @param string $endpoint The request endpoint.
	 * @param array  $args     Optional. The request arguments. Default empty.
	 * @param string $method   Optional. The request method. Default 'GET'.
	 * @return mixed The request response. WP_Error on failure.
	 */
	protected function request( string $endpoint, array $args = array(), string $method = 'GET' ) {
		$url  = $this->base_url . wp_unslash( $endpoint );
		$args = array_merge( array( 'app_id' => $this->app_id ), $args );

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
