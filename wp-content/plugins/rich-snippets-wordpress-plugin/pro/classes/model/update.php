<?php

namespace wpbuddy\rich_snippets\pro;

use wpbuddy\rich_snippets\WPBuddy_Model;
use function wpbuddy\rich_snippets\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Updates-
 *
 * Performs plugin updates (if any).
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
final class Update_Model {


	/**
	 * Collects data about the current plugin that is later sent to the update-check-server.
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public static function get_update_check_request_args(): array {

		$plugin_basename = plugin_basename( rich_snippets()->get_plugin_file() );

		$timeout = ( defined( 'DOING_CRON' ) && DOING_CRON )
		           || ( defined( 'WP_CLI' ) && WP_CLI )
			? 30 : 3;

		$options = array(
			'method'  => 'POST',
			'timeout' => $timeout,
			'body'    => array(
				'plugin_basename' => $plugin_basename,
				'purchase_code'   => get_option( 'wpb_rs/purchase_code', '' ),
				'php_version'     => PHP_VERSION,
			),
		);

		return $options;
	}


	/**
	 * Calls the update-check-server.
	 *
	 * @return Plugin_Update_Information|\WP_Error
	 * @since 2.0.0
	 *
	 */
	public static function retrieve_update_info() {

		$response = WPBuddy_Model::request(
			'/wpbuddy/rich_snippets_manager/v1/check-for-update',
			self::get_update_check_request_args(),
			true,
			Helper_Model::instance()->string_to_bool( $_GET['force-check'] ?? false )
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( ! isset( $response['data'] ) || ( isset( $response['data'] ) && ! is_array( $response['data'] ) ) ) {
			return new \WP_Error(
				'wpb-rs-update-info-error',
				__( 'The plugin was not able to retrieve update information. The response did not return anything.', 'rich-snippets-schema' )
			);
		}

		return new Plugin_Update_Information( $response['data'] );
	}


	/**
	 * Writes information into the
	 *
	 * @param bool $original
	 * @param string $action
	 * @param \stdClass $args
	 *
	 * @return bool|object
	 * @since 2.0.0
	 *
	 */
	public static function update_window_information( $original = false, string $action, $args ) {

		if ( 'plugin_information' != $action ) {
			return $original;
		}

		if ( ! isset( $args->slug ) ) {
			return $original;
		}

		$plugin_basename = $plugin_basename = plugin_basename( rich_snippets()->get_plugin_file() );
		$slug            = Helper_Model::instance()->get_slug_from_basename( $plugin_basename );

		if ( $args->slug !== $slug ) {
			return $original;
		}

		$plugin_updates = get_plugin_updates();

		if ( ! isset( $plugin_updates[ $plugin_basename ] ) ) {
			return $original;
		}

		if ( ! isset( $plugin_updates[ $plugin_basename ]->update ) ) {
			return $original;
		}

		return $plugin_updates[ $plugin_basename ]->update;
	}

}
