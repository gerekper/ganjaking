<?php

namespace WPMailSMTP\Pro\SmartRouting;

use WPMailSMTP\Options;
use WPMailSMTP\Pro\SmartRouting\Admin\SettingsTab;
use WPMailSMTP\Pro\WPMailArgs;
use WPMailSMTP\WP;

/**
 * Class SmartRouting.
 *
 * @since 3.7.0
 */
class SmartRouting {

	/**
	 * Register hooks.
	 *
	 * @since 3.7.0
	 */
	public function hooks() {

		// Add smart routing settings page.
		add_filter( 'wp_mail_smtp_admin_get_pages', [ $this, 'init_settings_tab' ] );

		// Filter options save process.
		add_filter( 'wp_mail_smtp_options_set', [ $this, 'filter_options_set' ] );

		/*
		 * Capture `wp_mail` function arguments and process smart routing.
		 * The Highest hook priority number tries to ensure to capture already filtered arguments.
		 */
		add_filter(
			'wp_mail',
			function ( $args ) {
				if ( $this->is_enabled() ) {
					$this->process_smart_routing( $args );
				}

				return $args;
			},
			PHP_INT_MAX
		);
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

		$tabs['routing'] = $settings_tab;

		return $tabs;
	}

	/**
	 * Process smart routing.
	 *
	 * @since 3.7.0
	 *
	 * @param array $args Array of the `wp_mail` function arguments.
	 */
	public function process_smart_routing( $args ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		$connections_manager = wp_mail_smtp()->get_connections_manager();

		// Bail if email connection was already set.
		if ( ! empty( $connections_manager->get_mail_connection( false ) ) ) {
			return;
		}

		$wp_mail_args = new WPMailArgs( $args );

		// Bail if it's a test email.
		if (
			$wp_mail_args->get_header( 'X-Mailer-Type' ) === 'WPMailSMTP/Admin/Test' ||
			$wp_mail_args->get_header( 'X-Mailer-Type' ) === 'WPMailSMTP/Admin/SetupWizard/Test'
		) {
			return;
		}

		$conditional_logic = new ConditionalLogic( $wp_mail_args );

		$routes = Options::init()->get( 'smart_routing', 'routes' );

		if ( ! empty( $routes ) ) {
			foreach ( $routes as $route ) {
				$connection_id = isset( $route['connection_id'] ) ? $route['connection_id'] : false;
				$conditionals  = isset( $route['conditionals'] ) ? $route['conditionals'] : false;

				$connection = $connections_manager->get_connection( $connection_id, false );

				if ( empty( $connection ) || empty( $conditionals ) ) {
					continue;
				}

				if ( $conditional_logic->process( $conditionals ) ) {
					$connections_manager->set_mail_connection( $connection );
					break;
				}
			}
		}
	}

	/**
	 * Sanitize options.
	 *
	 * @since 3.7.0
	 *
	 * @param array $options Currently processed options passed to a filter hook.
	 *
	 * @return array
	 */
	public function filter_options_set( $options ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh, Generic.Metrics.NestingLevel.MaxExceeded

		if ( ! isset( $options['smart_routing'] ) ) {
			$options['smart_routing'] = [
				'enabled' => false,
				'routes'  => [],
			];

			return $options;
		}

		foreach ( $options['smart_routing'] as $key => $value ) {
			if ( $key === 'enabled' ) {
				$options['smart_routing'][ $key ] = (bool) $value;
			} elseif ( $key === 'routes' && is_array( $value ) ) {
				foreach ( $value as $route_key => $route ) {
					if ( isset( $route['connection_id'] ) ) {
						$options['smart_routing'][ $key ][ $route_key ]['connection_id'] = sanitize_key( $route['connection_id'] );
					}

					if ( isset( $route['conditionals'] ) && is_array( $route['conditionals'] ) ) {
						$options['smart_routing'][ $key ][ $route_key ]['conditionals'] = WP::sanitize_text( $route['conditionals'] );
					}
				}
			}
		}

		return $options;
	}

	/**
	 * Whether smart routing is enabled.
	 *
	 * @since 3.7.0
	 *
	 * @return bool
	 */
	private function is_enabled() {

		return (bool) Options::init()->get( 'smart_routing', 'enabled' );
	}
}
