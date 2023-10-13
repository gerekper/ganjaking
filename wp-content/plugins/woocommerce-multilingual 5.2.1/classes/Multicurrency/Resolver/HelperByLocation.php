<?php

namespace WCML\MultiCurrency\Resolver;

use WCML\MultiCurrency\Geolocation;
use WCML\MultiCurrency\Settings;
use WPML\FP\Fns;

class HelperByLocation {

	/** @var null|callable $getCurrency */
	private static $getCurrency;

	/**
	 * @return string|null
	 */
	public static function getCurrencyByUserCountry() {
		if ( ! self::$getCurrency ) {
			self::$getCurrency = Fns::memorize( function() {
				$clientCountry = Geolocation::getUserCountry();
				$currency      = Geolocation::getOfficialCurrencyCodeByCountry( $clientCountry );

				if ( ! Settings::isValidCurrencyByCountry( $currency, $clientCountry ) ) {
					$currency = Settings::getFirstAvailableCurrencyByCountry( $clientCountry );
				}

				return $currency ?: null;
			} );
		}

		return call_user_func( self::$getCurrency );
	}
}
