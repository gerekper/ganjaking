<?php
/**
 * Admin Dashboard
 *
 * @package WC_Newsletter_Subscription/Admin
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Newsletter_Subscription_Admin_Dashboard.
 */
class WC_Newsletter_Subscription_Admin_Dashboard {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'init' ) );
		add_action( 'wp_ajax_wc_newsletter_subscription_refresh_stats_widget', array( $this, 'refresh_dashboard' ) );
	}

	/**
	 * Init dashboard widgets.
	 *
	 * @since 3.0.0
	 */
	public function init() {
		if ( ! current_user_can( 'manage_woocommerce' ) || ! wc_newsletter_subscription_provider_supports( 'stats' ) ) {
			return;
		}

		wp_add_dashboard_widget( 'wc_newsletter_subscription_stats', esc_html__( 'Newsletter subscribers', 'woocommerce-subscribe-to-newsletter' ), array( $this, 'stats_widget' ) );
	}

	/**
	 * Outputs the newsletter stats dashboard widget.
	 *
	 * @since 3.0.0
	 */
	public function stats_widget() {
		$provider  = wc_newsletter_subscription_get_provider();
		$stats     = $this->get_stats( $provider );
		$last_sync = $this->get_last_sync( $provider );

		include_once 'views/html-dashboard-widget-stats.php';
	}

	/**
	 * Gets the subscriber's stats.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Newsletter_Subscription_Provider $provider Provider object.
	 * @return array
	 */
	protected function get_stats( $provider ) {
		$stats = array();
		$list  = wc_newsletter_subscription_get_provider_list();

		if ( $list ) {
			$stats = $provider->get_formatted_stats( $list );
		}

		return $stats;
	}

	/**
	 * Gets last sync datetime string
	 *
	 * @since 3.1.0
	 *
	 * @param WC_Newsletter_Subscription_Provider $provider Provider object.
	 * @return string
	 */
	protected function get_last_sync( $provider ) {
		$list      = wc_newsletter_subscription_get_provider_list();
		$last_sync = '';

		if ( $list ) {
			$last_sync_datetime = $provider->get_last_sync( $list );

			if ( $last_sync_datetime ) {
				$settings_format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
				$last_sync       = $last_sync_datetime->date_i18n( $settings_format );
			}
		}

		return $last_sync;
	}

	/**
	 * Refresh Dashboard stats
	 *
	 * @since 3.1.0
	 */
	public function refresh_dashboard() {
		check_ajax_referer( 'refresh-newsletter-subscription-stats' );

		$provider = wc_newsletter_subscription_get_provider();
		$list     = wc_newsletter_subscription_get_provider_list();

		if ( $provider && $list ) {
			$provider->clear_stats( $list );

			ob_start();
			$this->stats_widget();
			wp_send_json( ob_get_clean() );
		}
	}
}

return new WC_Newsletter_Subscription_Admin_Dashboard();
