<?php

namespace WPMailSMTP\Pro\AdditionalConnections;

use WPMailSMTP\ConnectionInterface;
use WPMailSMTP\Pro\AdditionalConnections\Admin\SettingsTab;
use WPMailSMTP\Pro\AdditionalConnections\Admin\TestTab;
use WPMailSMTP\WP;

/**
 * Class AdditionalConnections.
 *
 * @since 3.7.0
 */
class AdditionalConnections {

	/**
	 * Register hooks.
	 *
	 * @since 3.7.0
	 */
	public function hooks() {

		// Add additional connections settings page.
		add_filter( 'wp_mail_smtp_admin_get_pages', [ $this, 'init_settings_tab' ] );

		// Maybe change connection object that should be used for OAuth authorization.
		add_filter( 'wp_mail_smtp_admin_pages_auth_tab_process_auth_connection', [ $this, 'process_auth_connection' ] );

		// Maybe change connection settings admin page url.
		add_filter(
			'wp_mail_smtp_admin_connection_settings_get_admin_page_url',
			function ( $admin_page_url, $connection ) {
				return ! $connection->is_primary() ? $this->get_connection_admin_page_url( $connection ) : $admin_page_url;
			},
			10,
			2
		);

		// Initialize test tab.
		( new TestTab() )->hooks();
	}

	/**
	 * Initialize settings tab.
	 *
	 * @since 3.7.0
	 *
	 * @param array $tabs Tabs array.
	 */
	public function init_settings_tab( $tabs ) {

		static $settings_tab = null;

		if ( is_null( $settings_tab ) ) {
			$settings_tab = new SettingsTab();
		}

		$tabs['connections'] = $settings_tab;

		return $tabs;
	}

	/**
	 * Set connection object that should be used for OAuth authorization
	 * based on the connection ID retrieved from the OAuth "state" GET param.
	 *
	 * @since 3.7.0
	 *
	 * @return ConnectionInterface
	 */
	public function process_auth_connection() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$state = isset( $_GET['state'] ) ? sanitize_key( $_GET['state'] ) : false;

		list( , $connection_id ) = array_pad( explode( '-', $state ), 2, false );

		if ( empty( $state ) || empty( $connection_id ) ) {
			wp_safe_redirect(
				add_query_arg( 'error', 'oauth_invalid_state', wp_mail_smtp()->get_admin()->get_admin_page_url() )
			);
			exit;
		}

		$connection = wp_mail_smtp()->get_connections_manager()->get_connection( $connection_id, false );

		if ( $connection === false ) {
			wp_safe_redirect(
				add_query_arg( 'error', 'oauth_invalid_connection', wp_mail_smtp()->get_admin()->get_admin_page_url() )
			);
			exit;
		}

		return $connection;
	}

	/**
	 * Get additional connection settings admin page URL.
	 *
	 * @since 3.7.0
	 *
	 * @param ConnectionInterface $connection The Connection object.
	 *
	 * @return string
	 */
	public function get_connection_admin_page_url( $connection ) {

		return add_query_arg(
			[
				'tab'           => 'connections',
				'mode'          => 'edit',
				'connection_id' => $connection->get_id(),
			],
			wp_mail_smtp()->get_admin()->get_admin_page_url()
		);
	}

	/**
	 * Get connection by ID.
	 *
	 * @since 3.7.0
	 *
	 * @param string $connection_id The connection ID.
	 *
	 * @return false|ConnectionInterface
	 */
	public function get_connection( $connection_id ) {

		if ( ! $this->connection_exists( $connection_id ) ) {
			return false;
		}

		return new Connection( $connection_id );
	}

	/**
	 * Get all connections.
	 *
	 * @since 3.7.0
	 *
	 * @return ConnectionInterface[]
	 */
	public function get_connections() {

		return array_map(
			function ( $connection_id ) {
				return new Connection( $connection_id );
			},
			array_keys( $this->get_connections_raw() )
		);
	}

	/**
	 * Get all configured connections.
	 *
	 * @since 3.7.0
	 *
	 * @return ConnectionInterface[]
	 */
	public function get_configured_connections() {

		return array_filter(
			$this->get_connections(),
			function ( $connection ) {
				return $connection->get_mailer()->is_mailer_complete();
			}
		);
	}

	/**
	 * Whether there are added additional connections.
	 *
	 * @since 3.7.0
	 *
	 * @return bool
	 */
	public function has_connections() {

		return ! empty( $this->get_connections_raw() );
	}

	/**
	 * Remove connection.
	 *
	 * @since 3.7.0
	 *
	 * @param string $connection_id The connection ID.
	 *
	 * @return bool
	 */
	public function remove_connection( $connection_id ) {

		$connections = $this->get_connections_raw();

		if ( ! isset( $connections[ $connection_id ] ) ) {
			return false;
		}

		unset( $connections[ $connection_id ] );

		if ( WP::use_global_plugin_settings() ) {
			update_blog_option( get_main_site_id(), ConnectionOptions::META_KEY, $connections );
		} else {
			update_option( ConnectionOptions::META_KEY, $connections, 'no' );
		}

		return true;
	}

	/**
	 * Check whether the connection exists.
	 *
	 * @since 3.7.0
	 *
	 * @param string $connection_id The connection ID.
	 *
	 * @return bool
	 */
	public function connection_exists( $connection_id ) {

		$additional_connections = $this->get_connections_raw();

		return isset( $additional_connections[ $connection_id ] );
	}

	/**
	 * Get additional connections page manage capability.
	 *
	 * @since 3.7.0
	 *
	 * @return string
	 */
	public function get_manage_capability() {

		/**
		 * Filter additional connections page manage capability.
		 *
		 * @since 3.7.0
		 *
		 * @param string $capability Additional connections page manage capability.
		 */
		return apply_filters( 'wp_mail_smtp_pro_additional_connections_get_manage_capability', 'manage_options' );
	}

	/**
	 * Get all connections raw data.
	 *
	 * @since 3.7.0
	 *
	 * @return array
	 */
	private function get_connections_raw() {

		if ( WP::use_global_plugin_settings() ) {
			return get_blog_option( get_main_site_id(), ConnectionOptions::META_KEY, [] );
		}

		return get_option( ConnectionOptions::META_KEY, [] );
	}
}
