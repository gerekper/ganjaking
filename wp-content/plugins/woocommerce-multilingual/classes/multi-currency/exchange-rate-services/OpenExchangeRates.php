<?php

namespace WCML\MultiCurrency\ExchangeRateServices;

/**
 * Class OpenExchangeRates
 */
class OpenExchangeRates extends Service {

	/**
	 * @return string
	 */
	public function getId() {
		return 'openexchangerates';
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'Open Exchange Rates';
	}

	/**
	 * @return string
	 */
	public function getUrl() {
		return 'https://openexchangerates.org/';
	}

	/**
	 * @return string
	 */
	public function getApiUrl() {
		return 'https://openexchangerates.org/api/latest.json?app_id=%1$s&base=%2$s&symbols=%3$s';
	}

	/**
	 * @return bool
	 */
	public function isKeyRequired() {
		return true;
	}

}
