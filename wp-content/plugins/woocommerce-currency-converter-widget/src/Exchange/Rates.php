<?php
/**
 * Handles the exchange rates for a specific currency.
 *
 * @since 2.0.0
 */

namespace KoiLab\WC_Currency_Converter\Exchange;

defined( 'ABSPATH' ) || exit;

use KoiLab\WC_Currency_Converter\Utilities\Exchange_Utils;

/**
 * Rates class.
 */
class Rates {

	/**
	 * Base Currency.
	 *
	 * The free plan only supports USD as the base currency.
	 *
	 * @var string
	 */
	protected $base = 'USD';

	/**
	 * Rates.
	 *
	 * @var array
	 */
	protected $rates;

	/**
	 * Gets the base currency.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_base() {
		return $this->base;
	}

	/**
	 * Gets all rates.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_all() {
		if ( is_null( $this->rates ) ) {
			$this->rates = $this->fetch_rates();
		}

		return $this->rates;
	}

	/**
	 * Gets the rate for the specified currency.
	 *
	 * @since 2.0.0
	 *
	 * @param string $currency Currency code.
	 * @return string|false
	 */
	public function get_rate( $currency ) {
		$rates = $this->get_all();

		return ( isset( $rates[ $currency ] ) ? $rates[ $currency ] : false );
	}

	/**
	 * Gets the rates for the currency list.
	 *
	 * @since 2.0.0
	 *
	 * @param array $currencies A list of currency codes.
	 * @return array
	 */
	public function get_rates( $currencies ) {
		return array_intersect_key( $this->get_all(), array_flip( $currencies ) );
	}

	/**
	 * Fetches the rates from the API.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function fetch_rates() {
		$transient_name = 'wc_currency_converter_widget_' . strtolower( $this->base ) . '_rates';
		$rates          = get_transient( $transient_name );

		if ( false === $rates ) {
			$provider = Exchange_Utils::get_provider();
			$rates    = $provider->get_rates( array( 'base' => $this->base ) );

			if ( ! empty( $rates ) ) {
				$expiration = ( $provider->get_refresh_period() * HOUR_IN_SECONDS );

				set_transient( $transient_name, $rates, $expiration );
			}
		}

		return $rates;
	}
}
