<?php

namespace WCML\MultiCurrency\Resolver;

use WCML\MultiCurrency\Geolocation;
use WCML\MultiCurrency\Settings;
use WPML\FP\Fns;
use function WCML\functions\getSitePress;

class HelperByLanguage {

	/** @var null|callable $getCurrency */
	private static $getCurrency;

	/**
	 * @param string $currentLang
	 *
	 * @return string|null
	 */
	public static function getCurrencyByUserCountry( $currentLang ) {
		if ( ! self::$getCurrency ) {
			self::$getCurrency = Fns::memorize( function() use ( $currentLang ) {
				$clientCountry = Geolocation::getUserCountry();
				$currency      = Geolocation::getOfficialCurrencyCodeByCountry( $clientCountry );

				if ( ! Settings::isValidCurrencyForLang( $currency, $currentLang ) ) {
					$currency = Settings::getFirstAvailableCurrencyForLang( $currentLang );
				}

				return $currency ?: null;
			} );
		}

		return call_user_func( self::$getCurrency );
	}


	/**
	 * @return string|null
	 */
	public static function getCurrentLanguage() {
		/** @var string|null $currentLang */
		$currentLang = getSitePress()->get_current_language();

		return in_array( $currentLang, [ 'all', null ], true )
			? getSitePress()->get_default_language()
			: $currentLang;
	}
}
