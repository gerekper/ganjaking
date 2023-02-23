<?php
/**
 * WooCommerce Payment Gateway Framework
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the plugin to newer
 * versions in the future. If you wish to customize the plugin for your
 * needs please refer to http://www.skyverge.com
 *
 * @package   SkyVerge/WooCommerce/Payment-Gateway/Classes
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2023, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_15 as Framework;

/**
 * Helper for authentication with the Intuit Payments API.
 *
 * @since 2.0.0
 */
class WC_Intuit_Payments_oAuth_Helper {


	/**
	 * Gets the common oAuth parameters.
	 *
	 * @since 2.0.0
	 * @param string $consumer_key the consumer key
	 * @return array
	 */
	public static function get_common_params( $consumer_key ) {

		$params = array(
			'oauth_nonce'            => wp_create_nonce( 'wc_' . wc_intuit_payments()->get_id() . '_api_oauth_request_nonce' ),
			'oauth_consumer_key'     => $consumer_key,
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_timestamp'        => time(),
			'oauth_version'          => '1.0',
		);

		return $params;
	}


	/**
	 * Generates the oAuth signature.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public static function generate_signature( $args = array() ) {

		$args = wp_parse_args( $args, array(
			'method'          => 'GET',
			'url'             => '',
			'params'          => array(),
			'consumer_secret' => '',
			'token_secret'    => '',
		) );

		ksort( $args['params'] );

		$values = array(
			strtoupper( $args['method'] ),
			rawurlencode( $args['url'] ),
			rawurlencode( http_build_query( $args['params'] ) ),
		);

		$key = $args['consumer_secret'] . '&' . $args['token_secret'];

		return rawurlencode( base64_encode( hash_hmac( 'sha1', implode( '&', $values ), $key, true ) ) );
	}


}
