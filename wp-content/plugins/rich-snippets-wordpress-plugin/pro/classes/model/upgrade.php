<?php

namespace wpbuddy\rich_snippets\pro;

use wpbuddy\rich_snippets\Admin_Settings_Controller;
use wpbuddy\rich_snippets\Cache_Model;
use wpbuddy\rich_snippets\WPBuddy_Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Upgrade.
 *
 * Performs upgrades (if any).
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
final class Upgrade_Model {

	/**
	 * Performs upgrades if any.
	 *
	 * @since 2.0.0
	 */
	public static function do_upgrades() {

		Cache_Model::clear_all_caches();

		Cron_Model::add_cron();

		self::jhztgj();

		self::init_options();
	}


	/**
	 * Initializes all options.
	 *
	 * Makes sure that all settings-options are in the database after the installation.
	 *
	 * @since 2.8.3
	 */
	public static function init_options() {
		foreach ( Admin_Settings_Controller::get_settings() as $section ) {
			foreach ( $section->get_settings() as $s ) {
				$s->init();
			}
		}

		$dismissed_rating_timestamp = intval( get_option( 'wpb_rs/rating_dismissed_timestamp', 0 ) );

		if ( $dismissed_rating_timestamp <= 0 ) {
			update_option( 'wpb_rs/rating_dismissed_timestamp', time(), true );
		}
	}


	/**
	 * TUFHSUMh
	 *
	 * @since 2.3.0
	 */
	public static function jhztgj() {

		$psk = get_option( base64_decode( 'd3BiX3JzL3B1cmNoYXNlX2NvZGU=' ), '' );

		if ( empty( $psk ) ) {
			return;
		}

		$response = WPBuddy_Model::request(
			base64_decode( 'L3dwYnVkZHkvcmljaF9zbmlwcGV0c19tYW5hZ2VyL3YxL3ZhbGlkYXRl' ),
			array(
				'method'  => 'POST',
				'body'    => array(
					base64_decode( 'cHVyY2hhc2VfY29kZQ==' ) => $psk,
				),
				'timeout' => 20,
			),
			false,
			true
		);

		if ( is_wp_error( $response ) ) {
			$error_data = $response->get_error_data();

			if ( ! isset( $error_data['body'] ) ) {
				return;
			}

			$error_data = json_decode( $error_data['body'] );

			if ( is_null( $error_data ) ) {
				return;
			}

			if ( ! isset( $error_data->code ) ) {
				return;
			}

			$response = new \stdClass();

			$response->{base64_decode( 'dmVyaWZpZWQ=' )} = false;
		}

		$v = isset( $response->{base64_decode( 'dmVyaWZpZWQ=' )} ) && $response->{base64_decode( 'dmVyaWZpZWQ=' )};

		update_option( base64_decode( 'd3BiX3JzL3ZlcmlmaWVk' ), $v, true );
		update_option( 'd3BiX3JzL3ZlcmlmaWVk', $v, true );
	}
}
