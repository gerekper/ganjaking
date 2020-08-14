<?php
/**
 * General functions
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Mailchimp
 * @version 2.0.10
 */

if ( ! defined( 'YITH_WCMC' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'yith_wcmc_doing_batch' ) ) {
	/**
	 * Check whether we're currently executing a batch
	 *
	 * @return bool Whether we're currently executing a batch
	 */
	function yith_wcmc_doing_batch() {
		return defined( 'YITH_WCMC_DOING_BATCH' ) && YITH_WCMC_DOING_BATCH;
	}
}

if ( ! function_exists( 'yith_wcmc_retrieve_legacy_groups' ) ) {
	/**
	 * Retrieve legacy groups from API 2.0
	 *
	 * @param $list_id string List id
	 *
	 * @return array Legacy groups
	 */
	function yith_wcmc_retrieve_legacy_groups( $list_id ) {
		$api_key = get_option( 'yith_wcmc_mailchimp_api_key' );

		if ( ! $api_key ) {
			return array();
		}

		$groups = get_transient( "yith_wcmc_legacy_groups_{$api_key}_{$list_id}" );

		if ( $groups ) {
			return $groups;
		} else {
			$ch = curl_init();

			if ( strstr( $api_key, "-" ) ) {
				list( $key, $dc ) = explode( "-", $api_key, 2 );
				if ( ! $dc ) {
					$dc = "us1";
				}
			}

			$root = str_replace( 'https://api', 'https://' . $dc . '.api', 'https://api.mailchimp.com/2.0/' );

			curl_setopt( $ch, CURLOPT_USERAGENT, 'MailChimp-PHP/2.0.6' );
			curl_setopt( $ch, CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_HEADER, false );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 30 );
			curl_setopt( $ch, CURLOPT_TIMEOUT, 600 );
			curl_setopt( $ch, CURLOPT_URL, "{$root}lists/interest-groupings.json" );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' ) );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( array( 'apikey' => $api_key, 'id' => $list_id ) ) );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

			$response_body = curl_exec( $ch );
			$info          = curl_getinfo( $ch );

			if ( curl_error( $ch ) ) {
				return array();
			}

			if ( floor( $info['http_code'] / 100 ) >= 4 ) {
				return array();
			}

			curl_close( $ch );

			$legacy_groups = json_decode( $response_body, true );

			if ( ! $legacy_groups ) {
				return array();
			}

			$legacy_groups = array_combine( wp_list_pluck( $legacy_groups, 'id' ), $legacy_groups );

			set_transient( "yith_wcmc_legacy_groups_{$api_key}_{$list_id}", $legacy_groups, DAY_IN_SECONDS );

			return $legacy_groups;
		}
	}
}

