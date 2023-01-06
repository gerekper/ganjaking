<?php

class WCML_Tracking_Link {

	// WPML general links.
	const WPML_HOME                 = 'https://wpml.org/';
	const WPML_ST_FAQ               = 'https://wpml.org/faq/how-to-add-string-translation-to-your-site/';
	const WPML_PURCHASE             = 'https://wpml.org/purchase/';
	const WPML_SUPPORT              = 'https://wpml.org/forums/forum/english-support/';
	const WPML_ACCOUNT              = 'https://wpml.org/shop/account/';
	const WPML_AUTO_TRANSLATION_DOC = 'https://wpml.org/documentation/automatic-translation/';

	// WCML general links.
	const WCML_MAIN_DOC                  = 'https://wpml.org/documentation/related-projects/woocommerce-multilingual/';
	const WCML_MULTI_CURRENCY_DOC        = 'https://wpml.org/documentation/related-projects/woocommerce-multilingual/multi-currency-support-woocommerce/';
	const WCML_CLEAR_CART_DOC            = 'https://wpml.org/documentation/related-projects/woocommerce-multilingual/clearing-cart-contents-when-language-or-currency-change/';
	const WCML_DISPLAY_AS_TRANSLATED_DOC = 'https://wpml.org/documentation/related-projects/woocommerce-multilingual/displaying-untranslated-products-in-secondary-languages/';

	// WCML compatibility links.
	const WCML_BOOKINGS_DOC            = 'https://wpml.org/documentation/woocommerce-extensions-compatibility/translating-woocommerce-bookings-woocommerce-multilingual/';
	const WCML_PRODUCT_ADDONS_DOC      = 'https://wpml.org/documentation/woocommerce-extensions-compatibility/translating-woocommerce-product-add-ons-woocommerce-multilingual/';
	const WCML_TAB_MANAGER_DOC         = 'https://wpml.org/documentation/woocommerce-extensions-compatibility/translating-woocommerce-tab-manager-woocommerce-multilingual/';
	const WCML_TABLE_RATE_SHIPPING_DOC = 'https://wpml.org/documentation/woocommerce-extensions-compatibility/translating-woocommerce-table-rate-shipping-woocommerce-multilingual/';
	const WCML_SUBSCRIPTIONS_DOC       = 'https://wpml.org/documentation/woocommerce-extensions-compatibility/translating-woocommerce-subscriptions-woocommerce-multilingual/';
	const WCML_CUSTOM_PRODUCT_TABS     = 'https://wpml.org/plugin/custom-product-tabs-for-woocommerce/';

	/**
	 * @param string       $link
	 * @param string|false $id     e.g. '#some-section'.
	 * @param array        $params
	 *
	 * @return string
	 */
	public static function generate( $link, $id = false, $params = [] ) {
		$params = array_merge( [
			'utm_source'   => 'plugin',
			'utm_medium'   => 'gui',
			'utm_campaign' => 'wcml',
		], $params );

		$link = add_query_arg( $params, $link );

		if ( $id ) {
			$link .= $id;
		}

		return $link;
	}

	/**
	 * @param bool $isStandalone
	 *
	 * @return array
	 */
	private static function getStandaloneParams( $isStandalone ) {
		return $isStandalone ? [ 'utm_campaign' => 'wcml-standalone' ] : [];
	}

	/**
	 * @param bool $isStandalone
	 *
	 * @return string
	 */
	public static function getWpmlHome( $isStandalone = false ) {
		return self::generate( self::WPML_HOME, false, self::getStandaloneParams( $isStandalone ) );
	}

	/**
	 * @param bool $isStandalone
	 *
	 * @return string
	 */
	public static function getWpmlStFaq( $isStandalone = false ) {
		return self::generate( self::WPML_ST_FAQ, false, self::getStandaloneParams( $isStandalone ) );
	}

	/**
	 * @param bool $isStandalone
	 *
	 * @return string
	 */
	public static function getWpmlPurchase( $isStandalone = false ) {
		return self::generate( self::WPML_PURCHASE, false, self::getStandaloneParams( $isStandalone ) );
	}

	/**
	 * @return string
	 */
	public static function getWpmlSupport() {
		return self::generate( self::WPML_SUPPORT );
	}

	/**
	 * @return string
	 */
	public static function getWpmlAccount() {
		return self::generate( self::WPML_ACCOUNT );
	}

	/**
	 * @return string
	 */
	public static function getWpmlAutoTranslationDoc() {
		return self::generate( self::WPML_AUTO_TRANSLATION_DOC );
	}

	/**
	 * @param false|string $id e.g. '#some-section'.
	 *
	 * @return string
	 */
	public static function getWcmlMainDoc( $id = false ) {
		return self::generate( self::WCML_MAIN_DOC, $id );
	}

	/**
	 * @param false|string $id e.g. '#some-section'.
	 *
	 * @return string
	 */
	public static function getWcmlMultiCurrencyDoc( $id = false ) {
		return self::generate( self::WCML_MULTI_CURRENCY_DOC, $id );
	}

	/**
	 * @return string
	 */
	public static function getWcmlClearCartDoc() {
		return self::generate( self::WCML_CLEAR_CART_DOC );
	}

	/**
	 * @return string
	 */
	public static function getWcmlDisplayAsTranslatedDoc() {
		return self::generate( self::WCML_DISPLAY_AS_TRANSLATED_DOC );
	}

	/**
	 * @return string
	 */
	public static function getWcmlBookingsDoc() {
		return self::generate( self::WCML_BOOKINGS_DOC );
	}

	/**
	 * @return string
	 */
	public static function getWcmlProductAddonsDoc() {
		return self::generate( self::WCML_PRODUCT_ADDONS_DOC );
	}

	/**
	 * @return string
	 */
	public static function getWcmlTabManagerDoc() {
		return self::generate( self::WCML_TAB_MANAGER_DOC );
	}

	/**
	 * @return string
	 */
	public static function getWcmlTableRateShippingDoc() {
		return self::generate( self::WCML_TABLE_RATE_SHIPPING_DOC );
	}

	/**
	 * @return string
	 */
	public static function getWcmlSubscriptionsDoc() {
		return self::generate( self::WCML_SUBSCRIPTIONS_DOC );
	}

	/**
	 * @return string
	 */
	public static function getWcmlCustomProductTabs() {
		return self::generate( self::WCML_CUSTOM_PRODUCT_TABS );
	}

}
