<?php

namespace WCML\MultiCurrency;

use WPML\FP\Fns;
use WPML\FP\Obj;
use WPML\FP\Relation;
use WPML\FP\Logic;

class Geolocation {

	const DEFAULT_COUNTRY_CURRENCY_CONFIG = '/res/geolocation/country-currency.json';

	const MODE_BY_LANGUAGE = Settings::MODE_BY_LANGUAGE;
	const MODE_BY_LOCATION = Settings::MODE_BY_LOCATION;

	/**
	 * @return bool
	 */
	public static function isUsed() {
		/** @var \woocommerce_wpml $woocommerce_wpml */
		global $woocommerce_wpml;

		$useDefaultCurrencyByLocation = function() use ( $woocommerce_wpml ) {
			return (bool) wpml_collect( $woocommerce_wpml->get_setting( 'default_currencies', [] ) )
				->first( Relation::equals( 'location' ) );
		};

		return wcml_is_multi_currency_on()
		       && ( Settings::isModeByLocation() || $useDefaultCurrencyByLocation() );
	}

	/**
	 * Get country code by user IP
	 *
	 * @return string
	 */
	private static function getCountryByUserIp() {
		wp_cache_add_non_persistent_groups( __CLASS__ );

		$isResolved = Logic::complement( Relation::equals( false ) );
		$key        = 'country';
		$country    = wp_cache_get( $key, __CLASS__ );
		
		if ( ! $isResolved( $country ) ) {
			$geolocationData = \WC_Geolocation::geolocate_ip( \WC_Geolocation::get_ip_address(), true );
			$country         = Obj::propOr( false, 'country', $geolocationData );
			
			if ( $isResolved( $country ) ) {
				wp_cache_add( $key, (string) $country, __CLASS__ );
			}
		}

		return (string) $country;
	}

	/**
	 * Get country currency config file
	 *
	 * @return array
	 */
	private static function parseConfigFile() {
		$config             = [];
		$configuration_file = WCML_PLUGIN_PATH . self::DEFAULT_COUNTRY_CURRENCY_CONFIG;

		if ( file_exists( $configuration_file ) ) {
			$json_content = file_get_contents( $configuration_file );
			$config       = json_decode( $json_content, true );
		}

		return $config;
	}

	/**
	 * @param string $country
	 *
	 * @return string|null
	 */
	public static function getOfficialCurrencyCodeByCountry( $country ) {
		return Obj::prop( $country, self::parseConfigFile() );
	}

	/**
	 * @return string
	 */
	public static function getUserCountry(){
		if ( defined( 'WCML_GEOLOCATED_COUNTRY' ) ) {
			return WCML_GEOLOCATED_COUNTRY;
		}

		$allUserCountries = [
			'billing'     => self::getUserCountryByAddress( 'billing' ),
			'shipping'    => self::getUserCountryByAddress( 'shipping' ),
			'geolocation' => self::getCountryByUserIp()
		];

		$userCountry = $allUserCountries['billing'] ?: $allUserCountries['geolocation'];

		/**
		 * This filter allows to override the address country declared by the user.
		 *
		 * @since 4.11.0
		 *
		 * @param string $userCountry Billing address used if set otherwise geolocation country used.
		 * @param array  $allUserCountries {
		 *      @type string $billing The billing address country
		 *      @type string $shipping The shipping address country
		 *      @type string $geolocation The geolocation country
		 * }
		 *
		 * @return string
		 */
		return apply_filters( 'wcml_geolocation_get_user_country', $userCountry, $allUserCountries );
	}

	/**
	 * Get country code from address if user logged-in.
	 *
	 * @param string $addressType Shipping or Billing address.
	 *
	 * @return string
	 */
	private static function getUserCountryByAddress( $addressType ){
		$orderCountry = self::getUserCountryFromOrder( $addressType );

		if( $orderCountry ){
			return $orderCountry;
		}

		$current_user_id = get_current_user_id();

		if ( $current_user_id ) {
			$customer = new \WC_Customer( $current_user_id, (bool) WC()->session );

			return 'shipping' === $addressType ? $customer->get_shipping_country() : $customer->get_billing_country();
		}

		return '';
	}

	/**
	 * Get country code from order based on address.
	 *
	 * @param string $addressType Shipping or Billing address.
	 *
	 * @return string
	 */
	private static function getUserCountryFromOrder( $addressType ) {
		$country = '';
		$isWcAjax  = Relation::propEq( 'wc-ajax', Fns::__, $_GET );

		if ( $isWcAjax( 'update_order_review' ) && isset( $_POST['country'] ) ) {
			$country = $_POST['country'];
		} elseif ( $isWcAjax( 'checkout' ) && isset( $_POST[ $addressType . '_country' ] ) ) {
			$country = $_POST[ $addressType . '_country' ];
		}

		return wc_clean( wp_unslash( $country ) );
	}
}