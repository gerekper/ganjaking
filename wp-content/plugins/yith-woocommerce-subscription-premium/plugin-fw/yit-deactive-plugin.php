<?php
/**
 * Functions for deactivating plugins.
 *
 * @package YITH\PluginFramework
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! function_exists( 'yit_deactive_free_version' ) ) {
	/**
	 * Deactivate the free version of the plugin.
	 *
	 * @param string $to_deactivate The constant name of the plugin to deactivate.
	 * @param string $to_activate   The path of the File of the plugin to activate.
	 *
	 * @deprecated  3.9.8
	 */
	function yit_deactive_free_version( $to_deactivate, $to_activate ) {
		yith_deactivate_plugins( $to_deactivate, $to_activate );
	}
}

if ( ! function_exists( 'yith_deactivate_plugins' ) ) {
	/**
	 *  Deactivate a list of plugins, and terminates activating another plugin.
	 *
	 * @param string|string[] $to_deactivate The constant name of the plugin(s) to deactivate.
	 * @param string          $to_activate   The path of the File of the plugin to activate.
	 */
	function yith_deactivate_plugins( $to_deactivate, $to_activate = false ) {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$deactivated   = array();
		$to_deactivate = (array) $to_deactivate;

		foreach ( $to_deactivate as $plugin_to_deactivate ) {
			if ( ! defined( $plugin_to_deactivate ) ) {
				continue;
			}

			$plugin_to_deactivate_init = constant( $plugin_to_deactivate );

			if ( ! is_plugin_active( $plugin_to_deactivate_init ) ) {
				continue;
			}

			deactivate_plugins( $plugin_to_deactivate_init );

			$deactivated[] = $plugin_to_deactivate_init;
		}

		if ( empty( $deactivated ) ) {
			return;
		}

		global $status, $page, $s;

		$query_params = array(
			'deactivated_plugins' => implode( ',', $deactivated ),
			'plugin_status'       => $status,
			'paged'               => $page,
			's'                   => $s,
		);

		if ( $to_activate && function_exists( 'wp_create_nonce' ) ) {
			$query_params = array_merge(
				$query_params,
				array(
					'action'   => 'activate',
					'plugin'   => $to_activate,
					'_wpnonce' => wp_create_nonce( 'activate-plugin_' . $to_activate ),
				)
			);
		}

		$redirect = esc_url_raw( add_query_arg( $query_params, admin_url( 'plugins.php' ) ) );

		if ( function_exists( 'wp_safe_redirect' ) ) {
			wp_safe_redirect( $redirect );
		} else {
			header( 'Location: ' . $redirect );
		}

		die;
	}
}

if ( ! function_exists( 'yith_print_deactivation_message' ) ) {
	/**
	 * Prints message about plugins deactivation, due to multiple versions active of the same software active at the same time
	 *
	 * @return void
	 * @since 3.9.8
	 */
	function yith_print_deactivation_message() {
		global $pagenow;

		// phpcs:disable WordPress.Security.NonceVerification
		if ( 'plugins.php' !== $pagenow || ! isset( $_GET['deactivated_plugins'] ) ) {
			return;
		}

		$names = sanitize_text_field( wp_unslash( $_GET['deactivated_plugins'] ) );
		$names = explode( ',', $names );
		$names = array_map(
			function( $name ) {
				$name = str_replace( array( '-', 'init.php', '/' ), ' ', $name );
				$name = str_replace( array( 'yith', 'woocommerce', 'wordpress' ), array( 'YITH', 'WooCommerce', 'WordPress' ), $name );

				return trim( ucwords( $name ) );
			},
			$names
		);

		// translators: 1. Plugin(s) name(s).
		$message = _n(
			'%s was deactivated as you\'re running an higher tier version of the same plugin.',
			'%s were deactivated as you\'re running higher tier versions of the same plugins.',
			count( $names ),
			'yit-plugin-fw'
		);
		$message = sprintf( $message, implode( ', ', $names ) );
		?>
		<div class="notice">
			<p><?php echo esc_html( $message ); ?></p>
		</div>
		<?php
		// phpcs:enable WordPress.Security.NonceVerification
	}
}

add_action( 'admin_notices', 'yith_print_deactivation_message' );
