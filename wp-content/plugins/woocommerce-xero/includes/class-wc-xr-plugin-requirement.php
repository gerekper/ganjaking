<?php
/**
 * This class handles plugin requirement validation.
 *
 * @package Woocommerce Xero
 * @since 1.7.52
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_XR_PLUGIN_REQUIREMENT.
 *
 * @since 1.7.52
 */
class WC_XR_PLUGIN_REQUIREMENT {
	/**
	 * Should return result about Woocommerce plugin availability status.
	 *
	 * @since 1.7.52
	 */
	public function is_woocommerce_active(): bool {
		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action(
				'admin_notices',
				static function () {
					$notice = sprintf(
						/* translators: 1. Woocommerce website link anchor tag */
						esc_html__(
							'WooCommerce Xero Integration requires %s to be installed and active.',
							'woocommerce-xero'
						),
						'<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>'
					);

					echo '<div class="error"><p>' . $notice . '</p></div>'; // phpcs:ignore
				}
			);

			return false;
		}

		return true;
	}

	/**
	 * Should return result about site SSL certificate status.
	 *
	 * @since 1.7.52
	 */
	public function is_ssl_active(): bool {
		if ( ! wp_is_https_supported() ) {
			add_action(
				'admin_notices',
				static function () {
					$https_errors = '';

					array_map(
						static function ( array $data ) use ( &$https_errors ) {
							$https_errors .= implode( ' ', $data );
						},
						get_option( 'https_detection_errors' )
					);

					$notice = sprintf(
					/* translators: 1: Xero Security Standards link anchor tag 2:WordPress HTTPS related documentation link anchor tag */
						esc_html__(
							'The WooCommerce Xero integration requires a valid SSL certificate to be installed and active, as per the %1$s. To learn more about setting up SSL certificates on WordPress, please read the %2$s on WordPress.org',
							'woocommerce-xero'
						),
						'<a href="https://developer.xero.com/partner/security-standard-for-xero-api-consumers" target="_blank">' .
						esc_html__( 'Xero Security Standards', 'woocommerce-xero' ) .
						'</a>',
						'<a href="https://wordpress.org/support/article/https-for-wordpress/" target="_blank">' .
						esc_html__( 'HTTPS for WordPress page', 'woocommerce-xero' ) .
						'</a>'
					);

					printf(
						'<div class="error"><p>%1$s</p><p><strong>%2$s</strong>: %3$s</p></div>',
						$notice, // phpcs:ignore
						esc_html__( 'Error Details', 'woocommerce-xero' ),
						esc_html( $https_errors )
					);
				}
			);

			return false;
		}

		return true;
	}
}
