<?php

namespace WCML\MultiCurrency\ExchangeRateServices;

use WPML\FP\Obj;

/**
 * Class Service
 */
abstract class Service {

	/** @var array  */
	private $settings;

	/**
	 * @return string
	 */
	abstract public function getId();

	/**
	 * @return string
	 */
	abstract public function getName();

	/**
	 * @return string
	 */
	abstract public function getUrl();

	/**
	 * @return string
	 */
	abstract public function getApiUrl();

	/**
	 * @return bool
	 */
	abstract public function isKeyRequired();

	/**
	 * @return void
	 */
	public function resetConnectionCache() {

	}

	/**
	 * @param string $from Base currency.
	 * @param array  $tos  Target currencies.
	 *
	 * @return mixed
	 * @throws \Exception Thrown where there are connection problems.
	 */
	public function getRates( $from, $tos ) {
		$this->clearLastError();

		$response = $this->makeRequest( $from, $tos );

		if ( is_wp_error( $response ) ) {
			$http_error = implode( "\n", $response->get_error_messages() );
			$this->saveLastError( $http_error );
			throw new \Exception( $http_error );
		}

		$data = json_decode( $response['body'] );

		if ( $this->isInvalidResponse( $data ) ) {
			$error = self::get_formatted_error( $data );
			$this->saveLastError( $error );
			throw new \Exception( $error );
		}

		return $this->extractRates( $data, $from, $tos );
	}

	/**
	 * @param string $from The base currency code.
	 * @param array  $tos  The target currency codes.
	 *
	 * @return array|\WP_Error
	 */
	protected function makeRequest( $from, $tos ) {
		if ( $this->isKeyRequired() ) {
			$url = sprintf( $this->getApiUrl(), $this->getApiKey(), $from, implode( ',', $tos ) );
		} else {
			$url = sprintf( $this->getApiUrl(), $from, implode( ',', $tos ) );
		}

		return wp_safe_remote_get( $url, [ 'headers' => $this->getRequestHeaders() ] );
	}

	/**
	 * @return array
	 */
	protected function getRequestHeaders() {
		return [];
	}

	/**
	 * @param object $decodedData
	 *
	 * @return bool
	 */
	protected function isInvalidResponse( $decodedData ) {
		return empty( $decodedData->rates );
	}

	/**
	 * @param object $validData
	 * @param string $from
	 * @param array  $tos
	 *
	 * @return array
	 */
	protected function extractRates( $validData, $from, $tos ) {
		$rates = [];

		foreach ( $validData->rates as $to => $rate ) {
			$rates[ $to ] = round( $rate, \WCML_Exchange_Rates::DIGITS_AFTER_DECIMAL_POINT );
		}

		return $rates;
	}

	/**
	 * Each service has its own response signature,
	 * and I also noticed that it does not always
	 * respect their own doc.
	 *
	 * So the idea is to just catch all possible information
	 * and return it as raw output.
	 *
	 * Example: "error_code: 104 - error_message: ..."
	 *
	 * @param array|\stdClass $response
	 *
	 * @return string
	 */
	public static function get_formatted_error( $response ) {
		// $getFromPath :: array -> string|null
		$getFromPath = function( $path ) use ( $response ) {
			try {
				$value = Obj::path( $path, $response );
				return is_string( $value ) || is_int( $value ) ? $value : null;
			} catch ( \Exception $e ) {
				return null;
			}
		};

		$formattedError = wpml_collect( [
			// Codes or types
			'error'         => $getFromPath( [ 'error' ] ),
			'error_code'    => $getFromPath( [ 'error', 'code' ] ),
			'error_type'    => $getFromPath( [ 'error', 'type' ] ),
			// Descriptions or messages
			'error_info'    => $getFromPath( [ 'error', 'info' ] ),
			'error_message' => $getFromPath( [ 'error', 'message' ] ),
			'message'       => $getFromPath( [ 'message' ] ),
			'description'   => $getFromPath( [ 'description' ] ),
		] )->filter()
		   ->map( function( $value, $key ) {
			   return "$key: $value";
		   } )
		   ->implode( ' - ' );

		return $formattedError
			? strip_tags( $formattedError )
			: esc_html__( 'Cannot get exchange rates. Connection failed.', 'woocommerce-multilingual' );
	}

	/**
	 * @return array
	 */
	public function getSettings() {
		if ( null === $this->settings ) {
			$this->settings = get_option( 'wcml_exchange_rate_service_' . $this->getId(), [] );
		}

		return $this->settings;
	}

	private function saveSettings() {
		update_option( 'wcml_exchange_rate_service_' . $this->getId(), $this->getSettings() );
	}

	/**
	 * @param string $key
	 *
	 * @return mixed|null
	 */
	public function getSetting( $key ) {
		return Obj::prop( $key, $this->getSettings() );
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 */
	public function saveSetting( $key, $value ) {
		$this->getSettings();
		$this->settings[ $key ] = $value;
		$this->saveSettings();
	}

	/**
	 * @param string $error_message
	 */
	public function saveLastError( $error_message ) {
		$this->saveSetting(
			'last_error',
			[
				'text' => $error_message,
				'time' => date_i18n( 'F j, Y g:i a', false, true ),
			]
		);
	}

	public function clearLastError() {
		$this->saveSetting( 'last_error', false );
	}

	/**
	 * @return mixed
	 */
	public function getLastError() {
		return $this->getSetting( 'last_error' );
	}

	/**
	 * @return string|null
	 */
	protected function getApiKey() {
		return $this->getSetting( 'api-key' );
	}
}
