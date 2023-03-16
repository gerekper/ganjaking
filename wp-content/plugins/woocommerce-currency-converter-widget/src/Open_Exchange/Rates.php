<?php
/**
 * Handles the exchange rates for a specific currency.
 *
 * @since 2.0.0
 */

namespace Themesquad\WC_Currency_Converter\Open_Exchange;

defined( 'ABSPATH' ) || exit;

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
			$rates = array();

			$app_id         = get_option( 'wc_currency_converter_app_id' );
			$refresh_period = get_option( 'wc_currency_converter_rates_refresh_period', 12 );

			// Fallback App ID.
			if ( ! $app_id ) {
				$app_id         = 'e65018798d4a4585a8e2c41359cc7f3c';
				$refresh_period = 12;
			}

			$response = ( new API( $app_id ) )->get_latest( array( 'base' => $this->base ) );

			if ( ! is_wp_error( $response ) && isset( $response['rates'] ) ) {
				$rates      = $response['rates'];
				$expiration = ( max( 1, $refresh_period ) * HOUR_IN_SECONDS );

				set_transient( $transient_name, $rates, $expiration );
			}
		}

		return $rates;
	}
}
