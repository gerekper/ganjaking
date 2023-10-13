<?php

namespace WCML\MultiCurrency\ExchangeRateServices;

/**
 * Class CurrencyLayer
 */
class CurrencyLayer extends ApiLayerService {

	/**
	 * @return string
	 */
	public function getId() {
		return 'currencylayer';
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'currencylayer';
	}

	/**
	 * @return string
	 */
	public function getUrl() {
		return 'https://currencylayer.com/';
	}

	/**
	 * @return string
	 */
	protected function getApiLayerUrl() {
		return 'https://api.apilayer.com/currency_data/live?source=%2$s&currencies=%3$s';
	}

	/**
	 * @return string
	 */
	protected function getApiLegacyUrl() {
		return 'http://apilayer.net/api/live?access_key=%s&source=%s&currencies=%s&amount=1';
	}

	/**
	 * @param object $decodedData
	 *
	 * @return bool
	 */
	protected function isInvalidResponse( $decodedData ) {
		return empty( $decodedData->quotes );
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

		foreach ( $tos as $to ) {
			if ( isset( $validData->quotes->{$from . $to} ) ) {
				$rates[ $to ] = round( $validData->quotes->{$from . $to}, \WCML_Exchange_Rates::DIGITS_AFTER_DECIMAL_POINT );
			}
		}

		return $rates;
	}
}
