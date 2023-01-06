<?php

namespace WCML\MultiCurrency\Resolver;

use WCML\MultiCurrency\Geolocation;
use WCML\MultiCurrency\Settings;
use WCML_Multi_Currency;

class ResolverForModeLocation implements Resolver {

	/**
	 * @inheritDoc
	 */
	public function getClientCurrency() {
		$storedCurrency = wcml_user_store_get( WCML_Multi_Currency::CURRENCY_STORAGE_KEY );

		// $isCurrencyInvalidInCountry :: string -> bool
		$isCurrencyInvalidInCountry = function( $currency ) {
			return ! Settings::isValidCurrencyByCountry( $currency, Geolocation::getUserCountry() );
		};

		if ( ! $storedCurrency ) {
			return HelperByLocation::getCurrencyByUserCountry();
		} elseif ( $isCurrencyInvalidInCountry( $storedCurrency ) ) {
			return HelperByLocation::getCurrencyByUserCountry();
		}

		return $storedCurrency;
	}
}
