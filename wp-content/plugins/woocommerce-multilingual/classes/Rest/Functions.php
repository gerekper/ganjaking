<?php

namespace WCML\Rest;

use WPML\FP\Obj;
use WPML\FP\Str;

class Functions {

	const STORE_NAMESPACE = 'wc/store/v1';

	/**
	 * Check if we are requesting a WooCommerce Analytics page.
	 *
	 * @return bool
	 */
	public static function isAnalyticsPage() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended,WordPress.VIP.SuperGlobalInputUsage.AccessDetected
		return is_admin()
			&& 'wc-admin' === Obj::prop( 'page', $_GET )
			&& 0 === strpos( sanitize_text_field( wp_unslash( Obj::prop( 'path', $_GET ) ) ), '/analytics/' );
		// phpcs:enable WordPress.Security.NonceVerification.Recommended,WordPress.VIP.SuperGlobalInputUsage.AccessDetected
	}

	/**
	 * Check if is request to the WooCommerce REST API.
	 *
	 * @return bool
	 */
	public static function isRestApiRequest() {
		return apply_filters( 'woocommerce_rest_is_request_to_rest_api', self::checkEndpoint( 'wc/v' . self::getApiRequestVersion() . '/' ) );
	}

	/**
	 * Check if is request to the WooCommerce Analytics REST API.
	 *
	 * @return bool
	 */
	public static function isAnalyticsRestRequest() {
		return self::checkEndpoint( 'wc-analytics/' );
	}

	/**
	 * Check if is request to the WooCommerce Store API.
	 *
	 * @return bool
	 */
	public static function isStoreAPIRequest() {
		return self::checkEndpoint( 'wc/store' );
	}

	/**
	 * Returns the version number of the API used for the current request
	 *
	 * @return int
	 */
	public static function getApiRequestVersion() {

		$version = 0;

		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			return $version;
		}

		$restPrefix = trailingslashit( rest_get_url_prefix() );
		if ( preg_match( '@' . $restPrefix . 'wc/v([0-9]+)/@i', $_SERVER['REQUEST_URI'], $matches ) ) {
			$version = intval( $matches[1] );
		}

		return $version;
	}

	/**
	 * @param string $endpoint
	 *
	 * @return bool
	 */
	private static function checkEndpoint( $endpoint = 'wc/' ) {
		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			return false;
		}

		$rest_prefix = trailingslashit( rest_get_url_prefix() );
		return ( false !== stripos( $_SERVER['REQUEST_URI'], $rest_prefix . $endpoint ) );
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return string|null
	 */
	public static function getStoreStrippedEndpoint( $request ) {
		$route = trim( $request->get_route(), '/' );

		if ( Str::startsWith( self::STORE_NAMESPACE, $route ) ) {
			return Str::sub( Str::len( trailingslashit( self::STORE_NAMESPACE ) ), $route );
		}

		return null;
	}

}
