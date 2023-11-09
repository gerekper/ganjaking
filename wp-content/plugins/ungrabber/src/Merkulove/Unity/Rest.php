<?php
/**
 * UnGrabber
 * A most effective way to protect your online content from being copied or grabbed
 * Exclusively on https://1.envato.market/ungrabber
 *
 * @encoding        UTF-8
 * @version         3.0.4
 * @copyright       (C) 2018 - 2023 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Commercial Software
 * @contributors    Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

namespace Merkulove\Ungrabber\Unity;

use WP_REST_Server;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

/**
 * Class adds admin js scripts.
 *
 * @since 1.0.0
 *
 **/
final class Rest {

	/**
	 * The one true Rest.
	 * @var Rest
	 **/
	private static $instance;

	/**
	 * Sets up a new REST instance.
	 * @access public
	 **/
	private function __construct() {

		add_action( 'rest_api_init', function () {

			register_rest_route(

				untrailingslashit( 'ungrabber/v2' ),
				'/(?P<action>\w+)/',
				array(
					'methods' => WP_REST_Server::ALLMETHODS,
					'callback' => [ $this, 'callback' ],
					'permission_callback' => '__return_true',
				)
			);

		} );

	}

	/**
	 * Rest callback
	 *
	 * @param $params
	 *
	 * @return void
	 */
	public function callback( $params ) {

		$action = $params[ 'action' ] ?? '';

		switch ( $action ) {

			case 'subscribe':

                // Prepare url
                $url = wp_sprintf(
                    'https://merkulove.host/wp-json/mdp/v2/%s?plugin=ungrabber&name=%s&mail=%s&domain=%s',
                    $action,
                    $params[ 'name' ] ?? '',
                    $params[ 'mail' ] ?? '',
                    $this->clear_url()
                );

				$remote = wp_remote_get( $url, $this->get_ssl_args() );
				$body = $remote[ 'body' ] ?? array();
				echo json_encode( $body );
				break;

            case 'dashboard':

                $data = $this->unity_dashboard_callback( $params );
                echo json_encode( $data );
                break;

			default:
				break;

		}

	}

	/**
	 * Prepare args for cURL request
	 * @return array
	 */
	private function get_ssl_args() {

		return [
			'timeout'    => 30,
			'user-agent' => 'ungrabber-user-agent',
			'sslverify'  => Settings::get_instance()->options[ 'check_ssl' ] === 'on'
		];

	}

    /**
     * Make url safe for queries
     * @return array|string|string[]
     */
    private function clear_url() {

        $protocols  = array( 'http://', 'http://www.', 'https://', 'https://www.', 'www.' );
        $url        = str_replace( $protocols, '', get_site_url() );

        return str_replace( '/', '-', $url );

    }

    /**
     * UPD dashboard handler
     * @return array|bool
     */
    private function unity_dashboard_callback( $params ) {

		$nonce = $params[ 'nonce' ] ?? '';
		check_ajax_referer( 'mdp-dashboard', $nonce );

        $result = false;
        $ask = $params[ 'ask' ] ?? '';
        switch ( $ask ) {

            case 'license':

                $result = $this->get_license_status( $params );
                break;

            case 'update':

                $cached = $this->get_update_status( $params );
                $result = json_decode( $cached, true )[ 'version' ] ?? '';
                break;

            default:
                break;

        }

        return $result;

    }

    /**
     * Get plugin table name of the cache table
     *
     * @param $plugin
     *
     * @return array|string
     */
    private function get_plugin_table_name( $plugin ) {

        global $wpdb;

        $table_name = str_replace( '-', '_', $plugin ) . '_cache';
        return esc_sql( $wpdb->prefix . $table_name );

    }

    /**
     * Fetch cached value from DB
     *
     * @param $plugin
     * @param $key
     *
     * @return int|bool
     */
    private function fetch_cached_value( $plugin, $key  ) {

        global $wpdb;

        // Check is table exists
        $table_name = $this->get_plugin_table_name( $plugin );
        $table_exists = $wpdb->get_var(
            $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name )
        );
        if( ! $table_exists ) { return false; }

        // Check is row exists
        $cache_row = $wpdb->get_row(
            $wpdb->prepare( "SELECT `data` FROM $table_name WHERE `key` = '%s'", $key ),
            ARRAY_A
        );
        if ( ! is_array( $cache_row ) || ! isset( $cache_row[ 'data' ] ) ) { return false; }

        // Check is data exists
        $cache = json_decode( $cache_row[ 'data' ], true );

        return $cache[ $key ] ?? false;

    }

    /**
     * Get license status
     *
     * @param $params
     *
     * @return bool
     */
    private function get_license_status( $params ) {

        // Get params from request
        $plugin = $params[ 'plugin' ] ?? '';
        if ( empty( $plugin ) ) { return false; }

        // Get cached value
        $plugin_id = $this->fetch_cached_value( $plugin, 'mdp_'. $plugin .'_envato_id' );
        if ( ! $plugin_id  ) { return false; }

        $pid = get_option( 'envato_purchase_code_' . $plugin_id, 0 );
        if ( $pid === 0 ) { return false; }

        return $this->fetch_cached_value( $plugin, 'activation_'. $pid );

    }

    private function get_update_status( $params ) {

        // Get params from request
        $plugin = $params[ 'plugin' ] ?? '';
        if ( empty( $plugin ) ) { return false; }

        // Get cached value
        $plugin_id = $this->fetch_cached_value( $plugin, 'mdp_'. $plugin .'_envato_id' );
        if ( ! $plugin_id  ) { return false; }

        $plugin_info = $this->fetch_cached_value( $plugin, 'mdp_'. str_replace( '-', '_', $plugin ) . '_plugin_info' );
        return $plugin_info[ 'body' ] ?? false;

    }

	/**
	 * Main Rest Instance.
	 * Insures that only one instance of Rest exists in memory at any one time.
	 *
	 * @static
	 * @return Rest
	 **/
	public static function get_instance() {

        /** @noinspection SelfClassReferencingInspection */
        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Rest ) ) {

			self::$instance = new Rest;

		}

		return self::$instance;

	}

}
