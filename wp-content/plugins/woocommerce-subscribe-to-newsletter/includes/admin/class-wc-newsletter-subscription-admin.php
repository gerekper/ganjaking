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
		add_action( 'current_screen', array( $this, 'conditional_includes' ) );
		add_action( 'admin_init', array( $this, 'process_settings_action' ) );
		add_action( 'admin_init', array( $this, 'add_notices' ), 30 );
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
		include_once 'class-wc-newsletter-subscription-admin-notices.php';
		include_once 'class-wc-newsletter-subscription-admin-system-status.php';

		// 'current_screen' is not triggered in AJAX requests.
		if ( wc_newsletter_subscription_is_request( 'ajax' ) ) {
			include_once 'class-wc-newsletter-subscription-admin-dashboard.php';
		}
	}

	/**
	 * Include admin files conditionally.
	 *
	 * @since 3.0.0
	 */
	public function conditional_includes() {
		$screen_id = wc_newsletter_subscription_get_current_screen_id();

		if ( in_array( $screen_id, array( 'dashboard', 'dashboard-network' ), true ) ) {
			include_once 'class-wc-newsletter-subscription-admin-dashboard.php';
		}
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

		if ( ! empty( $_GET['notice'] ) ) {
			$notice = wc_clean( wp_unslash( $_GET['notice'] ) );

			if ( 'disconnected' === $notice && ! wc_newsletter_subscription_is_connected() ) {
				WC_Admin_Settings::add_message( _x( 'Your newsletter service provider was disconnected successfully.', 'settings notice', 'woocommerce-subscribe-to-newsletter' ) );
			}
		}

		$action = ( ! empty( $_GET['action'] ) ? wc_clean( wp_unslash( $_GET['action'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification
		$nonce  = ( ! empty( $_GET['_wpnonce'] ) ? wp_unslash( $_GET['_wpnonce'] ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( ! $action || ! $nonce || ! wp_verify_nonce( $nonce, 'wc_newsletter_subscription_' . $action ) ) {
			return;
		}

		if ( 'disconnect' === $action && wc_newsletter_subscription_disconnect_provider() ) {
			wp_safe_redirect( wc_newsletter_subscription_get_settings_url( array( 'notice' => 'disconnected' ) ) );
		}
	}

	/**
	 * Adds the admin notices.
	 *
	 * @since 3.0.0
	 */
	public function add_notices() {
		if ( ! current_user_can( 'manage_woocommerce' ) || wc_newsletter_subscription_is_settings_page() ) {
			return;
		}

		$provider = wc_newsletter_subscription_get_provider();

		// The plugin required by the provider is not active.
		if ( $provider && method_exists( $provider, 'is_plugin_active' ) && ! $provider->is_plugin_active() ) {
			WC_Newsletter_Subscription_Admin_Notices::add_notice( 'wc_newsletter_subscription_provider_plugin_required' );
		}
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 2.8.0
	 */
	public function enqueue_scripts() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$screen_id = wc_newsletter_subscription_get_current_screen_id();
		$suffix    = wc_newsletter_subscription_get_scripts_suffix();

		if ( in_array( $screen_id, array( 'dashboard', 'dashboard-network' ), true ) && wc_newsletter_subscription_provider_supports( 'stats' ) ) {
			wp_enqueue_style( 'wc-newsletter-subscription-admin', WC_NEWSLETTER_SUBSCRIPTION_URL . 'assets/css/admin.css', array( 'woocommerce_admin_styles' ), WC_NEWSLETTER_SUBSCRIPTION_VERSION );
			wp_enqueue_script( 'wc-newsletter-subscription-dashboard', WC_NEWSLETTER_SUBSCRIPTION_URL . 'assets/js/admin/dashboard' . $suffix . '.js', array( 'jquery' ), WC_NEWSLETTER_SUBSCRIPTION_VERSION, true );
			wp_localize_script( 'wc-newsletter-subscription-dashboard', 'wc_newsletter_subscription_dashboard_params', array( 'nonce' => wp_create_nonce( 'refresh-newsletter-subscription-stats' ) ) );
		}

		if ( wc_newsletter_subscription_is_settings_page() ) {
			wp_enqueue_style( 'wc-newsletter-subscription-admin', WC_NEWSLETTER_SUBSCRIPTION_URL . 'assets/css/admin.css', array( 'woocommerce_admin_styles' ), WC_NEWSLETTER_SUBSCRIPTION_VERSION );
			wp_enqueue_script( 'wc-newsletter-subscription-settings', WC_NEWSLETTER_SUBSCRIPTION_URL . "assets/js/admin/settings{$suffix}.js", array( 'jquery' ), WC_NEWSLETTER_SUBSCRIPTION_VERSION, true );
			wp_localize_script( 'wc-newsletter-subscription-settings', 'wc_newsletter_subscription_settings_params', array( 'nonce' => wp_create_nonce( 'get-newsletter-subscription-lists' ) ) );
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
		if ( WC_NEWSLETTER_SUBSCRIPTION_BASENAME !== $file ) {
			return $links;
		}

		$links['docs'] = sprintf(
			'<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
			esc_url( 'https://woocommerce.com/document/newsletter-subscription/' ),
			esc_attr_x( 'View WooCommerce Newsletter Subscription Documentation', 'aria-label: documentation link', 'woocommerce-subscribe-to-newsletter' ),
			esc_html_x( 'Docs', 'plugin row link', 'woocommerce-subscribe-to-newsletter' )
		);

		$links['support'] = sprintf(
			'<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
			esc_url( 'https://woocommerce.com/my-account/create-a-ticket?select=18605' ),
			esc_attr_x( 'Open a support ticket at WooCommerce.com', 'aria-label: support link', 'woocommerce-subscribe-to-newsletter' ),
			esc_html_x( 'Support', 'plugin row link', 'woocommerce-subscribe-to-newsletter' )
		);

		return $links;
	}
}

return new WC_Newsletter_Subscription_Admin();
