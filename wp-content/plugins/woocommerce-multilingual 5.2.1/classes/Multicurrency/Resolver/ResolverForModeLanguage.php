<?php

namespace WCML\MultiCurrency\Resolver;

use WCML\MultiCurrency\Settings;
use WCML_Multi_Currency;
use WPML\FP\Logic;

class ResolverForModeLanguage implements Resolver {

	/**
	 * @inheritDoc
	 */
	public function getClientCurrency() {
		/** @var string $currentLang */
		$currentLang    = HelperByLanguage::getCurrentLanguage();
		$storedLang     = wcml_user_store_get( WCML_Multi_Currency::CURRENCY_LANGUAGE_STORAGE_KEY );
		$storedCurrency = wcml_user_store_get( WCML_Multi_Currency::CURRENCY_STORAGE_KEY );

		$getInitialCurrencyForLang = function() use ( $currentLang ) {
			return Settings::isDefaultCurrencyByLocationForLang( $currentLang )
				? HelperByLanguage::getCurrencyByUserCountry( $currentLang )
				: Settings::getDefaultCurrencyForLang( $currentLang );
		};

		$reInitCurrencyIfLangHasChanged = function() use ( $currentLang, $storedLang, $storedCurrency, $getInitialCurrencyForLang ) {
			$hasChangedLang = $storedLang && $currentLang !== $storedLang;

			if ( $hasChangedLang ) {
				$initialCurrencyForLang = $getInitialCurrencyForLang();

				if ( $initialCurrencyForLang ) {
					$preventSwitching = apply_filters( 'wcml_switch_currency_exception', false, $storedCurrency, $initialCurrencyForLang, true );

					if ( ! array_key_exists( 'force_switch', $_POST ) && $preventSwitching ) {
						do_action( 'wcml_multi_currency_set_switching_currency_html', $preventSwitching['prevent_switching'] );
					}

					return $initialCurrencyForLang;
				}
			}

			return null;
		};

		$getStoredCurrencyIfValid = function() use ( $currentLang, $storedCurrency ) {
			return Settings::isValidCurrencyForLang( $storedCurrency, $currentLang )
				? $storedCurrency
				: null;
		};

		$getDefaultCurrencyIfValid = function() use ( $currentLang ) {
			$defaultCurrency = wcml_get_woocommerce_currency_option();

			return Settings::isValidCurrencyForLang( $defaultCurrency, $currentLang )
				? $defaultCurrency
				: null;
		};

		$getFirstAvailableCurrencyForLang = function() use ( $currentLang ) {
			return Settings::getFirstAvailableCurrencyForLang( $currentLang );
		};

		$resolve = Logic::firstSatisfying(
			Logic::isTruthy(),
			[
				$reInitCurrencyIfLangHasChanged,
				$getStoredCurrencyIfValid,
				$getInitialCurrencyForLang,
				$getDefaultCurrencyIfValid,
				$getFirstAvailableCurrencyForLang,
			]
		);

		return $resolve( null );
	}
}
