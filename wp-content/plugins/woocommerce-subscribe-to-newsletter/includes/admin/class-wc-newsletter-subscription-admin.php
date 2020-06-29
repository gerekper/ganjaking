<?php
/**
 * Newsletter Subscription Admin
 *
 * @package WC_Newsletter_Subscription/Admin
 * @since   2.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Newsletter_Subscription_Admin.
 */
class WC_Newsletter_Subscription_Admin {

	/**
	 * Constructor.
	 *
	 * @since 2.6.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'admin_init', array( $this, 'process_settings_action' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_settings_page' ) );

		add_filter( 'plugin_action_links_' . WC_NEWSLETTER_SUBSCRIPTION_BASENAME, array( $this, 'action_links' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );

	}

	/**
	 * Include any classes we need within admin.
	 *
	 * @since 2.8.0
	 */
	public function includes() {
		include_once 'wc-newsletter-subscription-admin-functions.php';
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 2.8.0
	 */
	public function enqueue_scripts() {
		if ( ! wc_newsletter_subscription_is_settings_page() ) {
			return;
		}

		$suffix = wc_newsletter_subscription_get_scripts_suffix();

		wp_enqueue_script( 'wc-newsletter-subscription-settings', WC_NEWSLETTER_SUBSCRIPTION_URL . "assets/js/admin/settings{$suffix}.js", array( 'jquery' ), WC_NEWSLETTER_SUBSCRIPTION_VERSION, true );
	}

	/**
	 * Processes the settings action.
	 *
	 * @since 2.8.0
	 */
	public function process_settings_action() {
		if ( ! wc_newsletter_subscription_is_settings_page() ) {
			return;
		}

		$action = ( ! empty( $_GET['action'] ) ? wc_clean( wp_unslash( $_GET['action'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification
		$nonce  = ( ! empty( $_GET['_wpnonce'] ) ? wp_unslash( $_GET['_wpnonce'] ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( $action && $nonce && wp_verify_nonce( $nonce, 'wc_newsletter_subscription_' . $action ) ) {
			if ( 'disconnect' === $action ) {
				if ( wc_newsletter_subscription_disconnect_provider() ) {
					wp_safe_redirect( wc_newsletter_subscription_get_settings_url( array( 'notice' => 'disconnected' ) ) );
				}
			}
		} elseif ( ! empty( $_GET['notice'] ) ) {
			$notice = wc_clean( wp_unslash( $_GET['notice'] ) );

			if ( 'disconnected' === $notice && ! wc_newsletter_subscription_is_connected() ) {
				WC_Admin_Settings::add_message( _x( 'Your newsletter service provider was disconnected successfully.', 'settings notice', 'woocommerce-subscribe-to-newsletter' ) );
			}
		}
	}

	/**
	 * Adds the settings page.
	 *
	 * @since 2.8.0
	 *
	 * @param array $settings The settings pages.
	 * @return array An array with the settings pages.
	 */
	public function add_settings_page( $settings ) {
		$settings[] = include 'class-wc-newsletter-subscription-admin-settings.php';

		return $settings;
	}

	/**
	 * Adds custom links to the plugins page.
	 *
	 * @since 2.6.0
	 *
	 * @param array $links The plugin links.
	 * @return array The filtered plugin links.
	 */
	public function action_links( $links ) {
		$settings_link = sprintf(
			'<a href="%1$s" aria-label="%2$s">%3$s</a>',
			esc_url( wc_newsletter_subscription_get_settings_url() ),
			_x( 'View WooCommerce Newsletter Subscription Settings', 'aria-label: settings link', 'woocommerce-subscribe-to-newsletter' ),
			_x( 'Settings', 'plugin action link', 'woocommerce-subscribe-to-newsletter' )
		);

		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Adds custom links to this plugin on the plugins screen.
	 *
	 * @since 2.6.0
	 *
	 * @param mixed $links Plugin Row Meta.
	 * @param mixed $file  Plugin Base file.
	 * @return array
	 */
	public function plugin_row_meta( $links, $file ) {
		if ( WC_NEWSLETTER_SUBSCRIPTION_BASENAME === $file ) {
			$row_meta = array(
				'docs' => sprintf(
					'<a href="%1$s" aria-label="%2$s">%3$s</a>',
					esc_url( 'https://docs.woocommerce.com/document/newsletter-subscription/' ),
					esc_attr_x( 'View WooCommerce Newsletter Subscription Documentation', 'aria-label: documentation link', 'woocommerce-subscribe-to-newsletter' ),
					esc_html_x( 'Docs', 'plugin row link', 'woocommerce-subscribe-to-newsletter' )
				),
			);

			$links = array_merge( $links, $row_meta );
		}

		return $links;
	}
}

return new WC_Newsletter_Subscription_Admin();
