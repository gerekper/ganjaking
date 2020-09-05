<?php

namespace wpbuddy\rich_snippets\pro;

use wpbuddy\rich_snippets\WPBuddy_Model;
use function wpbuddy\rich_snippets\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Update Controller.
 *
 * Performs plugin updates.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
final class Update_Controller {


	/**
	 * Hooks into update transient.
	 *
	 * Note that this is fired multiple times. So we need to cache it.
	 *
	 * @hooked site_transient_update_plugins
	 *
	 * @param mixed $transient_value
	 *
	 * @return mixed
	 * @since  2.0.0
	 *
	 */
	public static function transient_hook( $transient_value ) {

		if ( ! isset( $transient_value->response ) ) {
			return $transient_value;
		}

		$found = false;
		$cache = wp_cache_get( 'update_info', 'wpb_rs', false, $found );

		if ( rich_snippets()->debug() ) {
			$found = false;
		}

		# cache was found
		if ( $found ) {
			# check if cache actually has information
			if ( isset( $cache->basename ) ) {
				$transient_value->response[ $cache->basename ] = $cache;
			} else {
				# if cache has no information, something went wrong during the request.
				return $transient_value;
			}
		} else {
			# cache was not found, retrieve update information now
			$plugin_info = self::get_updates();

			# stop if we got error
			if ( is_wp_error( $plugin_info ) ) {
				wp_cache_set( 'update_info', 0, 'wpb_rs', 5 * MINUTE_IN_SECONDS );

				return $transient_value;
			}

			# stop if we don't have a new version
			if ( ! self::is_new_version( $plugin_info->new_version ) ) {
				wp_cache_set( 'update_info', 0, 'wpb_rs', 5 * MINUTE_IN_SECONDS );

				return $transient_value;
			}

			wp_cache_set( 'update_info', $plugin_info, 'wpb_rs', 5 * MINUTE_IN_SECONDS );

			$transient_value->response[ $plugin_info->basename ] = $plugin_info;
		}

		return $transient_value;
	}


	/**
	 * Checks for updates.
	 *
	 * @return Plugin_Update_Information|\WP_Error
	 * @since 2.0.0
	 *
	 */
	public static function get_updates() {

		# if someone hits the 'force check' button always fetch new data.
		if ( isset( $_GET['force-check'] ) ) {
			$cache = false;
		} else {
			$cache = get_transient( 'wpb_rs_update_info' );
		}

		if ( ! rich_snippets()->debug() ) {

			# null means: something is in the cache but it's not valid. Maybe update server is not available.
			if ( '0' === $cache ) {
				return new \WP_Error(
					'wpb-rs-get-updates',
					__( 'It seems the update server is not reachable at the moment. Please stay tuned.', 'rich-snippets-schema' )
				);
			}

			# yey! There is something in the cache.
			if ( $cache instanceof Plugin_Update_Information ) {
				return $cache;
			}
		}

		unset( $cache );

		$plugin_information = Update_Model::retrieve_update_info();

		if ( is_wp_error( $plugin_information ) ) {
			set_transient( 'wpb_rs_update_info', 0, DAY_IN_SECONDS );

			return $plugin_information;
		}

		set_transient( 'wpb_rs_update_info', $plugin_information, DAY_IN_SECONDS );

		return $plugin_information;
	}


	/**
	 * Adds auth headers when WordPress downloads a package.
	 *
	 * @param array $args
	 * @param string $url
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public static function download_auth_headers( $args, $url ) {

		# make sure this is a call to the WP-Buddy API
		if ( false === stripos( $url, '/wpbuddy/rich_snippets_manager' ) ) {
			return $args;
		}

		# make sure this is a call to the get-update-file REST route
		if ( false === stripos( $url, '/get-update-file' ) ) {
			return $args;
		}

		$args = WPBuddy_Model::request_args( $args );

		return $args;
	}


	/**
	 * Checks if the current plugin version is lower than the given one.
	 *
	 * @param string $version
	 *
	 * @return bool
	 * @since 2.0.0
	 *
	 */
	public static function is_new_version( $version ) {

		$plugin_data = Helper_Model::instance()->get_plugin_data();

		return version_compare( $plugin_data['Version'], $version, '<' );
	}


}
