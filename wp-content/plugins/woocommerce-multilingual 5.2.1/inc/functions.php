<?php

namespace WCML\functions;

use function WPML\Container\make;
use WCML\StandAlone\NullSitePress;
use SitePress;
use woocommerce_wpml;

if ( ! function_exists( 'WCML\functions\getSitePress' ) ) {
	/**
	 * @global SitePress|null $sitepress
	 * @return SitePress|NullSitePress
	 */
	function getSitePress() {
		global $sitepress;

		if ( null === $sitepress ) {
			return new NullSitePress();
		}
		return $sitepress;
	}
}

if ( ! function_exists( 'WCML\functions\getWooCommerceWpml' ) ) {
	/**
	 * @return woocommerce_wpml
	 */
	function getWooCommerceWpml() {
		/**
		 * @global woocommerce_wpml $woocommerce_wpml
		 */
		global $woocommerce_wpml;

		return $woocommerce_wpml;
	}
}

if ( ! function_exists( 'WCML\functions\isStandAlone' ) ) {
	/**
	 * Test whether we are running in standalone mode.
	 *
	 * @return bool
	 */
	function isStandAlone() {
		return ! defined( 'ICL_SITEPRESS_VERSION' );
	}
}

if ( ! function_exists( 'WCML\functions\assetLink' ) ) {
	/**
	 * Return correct link to asset
	 *
	 * @param  string $asset
	 * @return string
	 */
	function assetLink( $asset ) {
		if ( isStandAlone() ) {
			return WCML_PLUGIN_URL . '/addons/wpml-dependencies/lib' . $asset;
		}
		return ICL_PLUGIN_URL . $asset;
	}
}

if ( ! function_exists( '\WCML\functions\getSetting' ) ) {
	/**
	 * @param string $key
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	function getSetting( $key, $default = null ) {
		return make( woocommerce_wpml::class )->get_setting( $key, $default );
	}
}

if ( ! function_exists( '\WCML\functions\updateSetting' ) ) {
	/**
	 * @param string $key
	 * @param mixed  $value
	 * @param bool   $autoload
	 *
	 * @return void
	 */
	function updateSetting( $key, $value, $autoload = false ) {
		make( woocommerce_wpml::class )->update_setting( $key, $value, $autoload );
	}
}

if ( ! function_exists( '\WCML\functions\getClientCurrency' ) ) {
	/**
	 * @return string
	 */
	function getClientCurrency() {
		return make( \WCML_Multi_Currency::class )->get_client_currency();
	}
}

if ( ! function_exists( 'WCML\functions\isCli' ) ) {
	/**
	 * @return bool
	 */
	function isCli() {
		return defined( 'WP_CLI' ) && WP_CLI;
	}
}
