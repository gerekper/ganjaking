<?php

namespace WCML\MultiCurrency;

use WPML\FP\Fns;
use WPML\FP\Lst;
use WPML\FP\Obj;
use WPML\FP\Relation;
use function WCML\functions\getSetting;
use function WCML\functions\isStandAlone;
use function WCML\functions\updateSetting;

class Settings {

	const MODE_BY_LANGUAGE = 'by_language';
	const MODE_BY_LOCATION = 'by_location';

	/**
	 * @return string|null
	 */
	public static function getMode() {
		$persistedMode = getSetting( 'currency_mode' );

		// Force location mode at runtime in standalone
		// to preserve settings in case of temporary change.
		if ( self::MODE_BY_LANGUAGE === $persistedMode && isStandAlone() ) {
			return self::MODE_BY_LOCATION;
		}

		return $persistedMode;
	}

	/**
	 * @return bool
	 */
	public static function isModeByLanguage() {
		return self::getMode() === self::MODE_BY_LANGUAGE;
	}

	/**
	 * @return bool
	 */
	public static function isModeByLocation() {
		return self::getMode() === self::MODE_BY_LOCATION;
	}

	/**
	 * @param string $mode
	 *
	 * @return void
	 */
	public static function setMode( $mode ) {
		updateSetting( 'currency_mode', $mode );
	}

	/**
	 * @return bool
	 */
	public static function isDisplayOnlyCustomPrices() {
		return (bool) getSetting( 'display_custom_prices' );
	}

	/**
	 * @param string $code
	 *
	 * @return bool
	 */
	public static function isActiveCurrency( $code ) {
		return Lst::includes( $code, self::getActiveCurrencyCodes() );
	}

	/**
	 * @return array
	 */
	public static function getActiveCurrencyCodes() {
		return Obj::keys( self::getCurrenciesOptions() );
	}

	/**
	 * @return array
	 */
	public static function getCurrenciesOptions() {
		return (array) getSetting( 'currency_options' );
	}

	/**
	 * @param string|array $keyOrPath
	 * @param mixed|null   $default
	 *
	 * @return mixed
	 */
	public static function getCurrenciesOption( $keyOrPath, $default = null ) {
		return Obj::pathOr( $default, (array) $keyOrPath, self::getCurrenciesOptions() );
	}

	/**
	 * @param string $currency
	 * @param string $clientCountry
	 *
	 * @return bool
	 */
	public static function isValidCurrencyByCountry( $currency, $clientCountry ) {
		$currencySettings = self::getCurrenciesOption( $currency );

		// $isLocationMode :: string -> bool
		$isLocationMode = Relation::propEq( 'location_mode', Fns::__, $currencySettings );

		// $containsCountry :: string -> bool
		$containsCountry = Lst::includes( Fns::__, (array) Obj::prop( 'countries', $currencySettings ) );

		if ( $isLocationMode( 'all' ) ) {
			return true;
		} elseif ( $isLocationMode( 'include' ) && $containsCountry( $clientCountry ) ) {
			return true;
		} elseif ( $isLocationMode( 'exclude' ) && ! $containsCountry( $clientCountry ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @param string $country
	 *
	 * @return string|null
	 */
	public static function getFirstAvailableCurrencyByCountry( $country ) {
		// $isValidCurrency :: string -> bool
		$isValidCurrency = function( $currency ) use ( $country ) {
			return self::isValidCurrencyByCountry( $currency, $country );
		};

		return wpml_collect( self::getActiveCurrencyCodes() )->first( $isValidCurrency );
	}

	/**
	 * @return array
	 */
	public static function getDefaultCurrencies() {
		return (array) getSetting( 'default_currencies' );
	}

	/**
	 * @return array
	 */
	public static function getOrderedCurrencyCodes() {
		return (array) getSetting( 'currencies_order' ) ?: self::getActiveCurrencyCodes();
	}

	/**
	 * @param string $lang
	 *
	 * @return bool
	 */
	public static function isDefaultCurrencyByLocationForLang( $lang ) {
		return 'location' === self::getDefaultCurrencyForLang( $lang );
	}

	/**
	 * @param string $lang
	 *
	 * @return string A real currency (e.g. "USD") or "location" or a falsy string for "keep" (default).
	 */
	public static function getDefaultCurrencyForLang( $lang ) {
		return (string) Obj::propOr( '', $lang, self::getDefaultCurrencies() );
	}

	/**
	 * @param string $currency
	 * @param string $lang
	 *
	 * @return bool
	 */
	public static function isValidCurrencyForLang( $currency, $lang ) {
		return (bool) self::getCurrenciesOption( [ $currency, 'languages', $lang ] );
	}

	/**
	 * @param string $lang
	 *
	 * @return string|null
	 */
	public static function getFirstAvailableCurrencyForLang( $lang ) {
		return (string) wpml_collect( self::getCurrenciesOptions() )
			->filter( Obj::path( [ 'languages', $lang ] ) )
			->keys()
			->first();
	}

	/**
	 * @return bool
	 */
	public static function isAutomaticRateEnabled() {
		return (bool) Obj::prop( 'automatic', self::getAutomaticRateSettings() );
	}

	/**
	 * @return array
	 */
	private static function getAutomaticRateSettings() {
		return Obj::propOr( [], 'exchange_rates', getSetting( 'multi_currency', [] ) );
	}
}
