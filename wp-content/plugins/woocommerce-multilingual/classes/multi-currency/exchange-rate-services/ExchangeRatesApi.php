<?php

namespace WCML\MultiCurrency\ExchangeRateServices;

/**
 * Class ExchangeRatesApi
 */
class ExchangeRatesApi extends ApiLayerService {

	/**
	 * @return string
	 */
	public function getId() {
		return 'exchangeratesapi';
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'Exchange rates API';
	}

	/**
	 * @return string
	 */
	public function getUrl() {
		return 'https://exchangeratesapi.io/';
	}

	/**
	 * @return string
	 */
	protected function getApiLayerUrl() {
		return 'https://api.apilayer.com/exchangerates_data/latest?base=%2$s&symbols=%3$s&amount=1';
	}

	/**
	 * @return string
	 */
	protected function getApiLegacyUrl() {
		return 'http://api.exchangeratesapi.io/v1/latest?access_key=%1$s&base=%2$s&symbols=%3$s';
	}
}
