<?php
/**
 * System Status Report.
 *
 * Adds extra information related to Newsletter Subscription to the system status report.
 *
 * @package WC_Newsletter_Subscription/Admin
 * @since   3.3.3
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Newsletter_Subscription_Admin_System_Status.
 */
class WC_Newsletter_Subscription_Admin_System_Status {

	/**
	 * Init.
	 *
	 * @since 3.3.3
	 */
	public static function init() {
		add_action( 'woocommerce_system_status_report', array( __CLASS__, 'output_content' ) );
	}

	/**
	 * Outputs the Newsletter Subscription in the system status report.
	 *
	 * @since 3.3.3
	 */
	public static function output_content() {
		$provider = wc_newsletter_subscription_get_provider();

		$checkout_location_choices = wc_newsletter_subscription_get_checkout_location_choices();
		$checkout_location         = get_option( 'woocommerce_newsletter_checkout_location', 'after_terms' );

		$order_statuses                       = wc_get_order_statuses();
		$selected_subscribe_on_order_statuses = get_option( 'woocommerce_newsletter_order_statuses', array() );
		$subscribe_on_order_statuses          = array();

		if ( ! empty( $selected_subscribe_on_order_statuses ) ) {
			foreach ( $selected_subscribe_on_order_statuses as $order_status ) {
				$subscribe_on_order_statuses[ $order_status ] = $order_statuses[ $order_status ];
			}
		}

		$data = array(
			'provider_list'               => '',
			'subscribe_checkbox_label'    => get_option( 'woocommerce_newsletter_label', '' ),
			'default_checkbox_status'     => get_option( 'woocommerce_newsletter_checkbox_status', 'checked' ),
			'checkout_location'           => $checkout_location_choices[ $checkout_location ],
			'subscribe_on_order_statuses' => $subscribe_on_order_statuses,
		);

		if ( $provider ) {
			$data['provider_list'] = wc_newsletter_subscription_get_provider_list( $provider );

			if ( 'mailchimp' === $provider->get_id() ) {
				$data['mailchimp_double_opt_in'] = wc_string_to_bool( get_option( 'woocommerce_mailchimp_double_opt_in', 'yes' ) );
			}
		}

		include_once dirname( __FILE__ ) . '/views/html-admin-status-report-settings.php';
	}

	/**
	 * Outputs the HTML content for the boolean value.
	 *
	 * @param bool $value The bool to format.
	 *
	 * @since 3.3.3
	 */
	public static function output_bool_html( $value ) {
		printf(
			'<mark class="%1$s"><span class="dashicons dashicons-%2$s"></span></mark>',
			esc_attr( $value ? 'yes' : 'error' ),
			esc_attr( $value ? 'yes' : 'no-alt' )
		);
	}
}

WC_Newsletter_Subscription_Admin_System_Status::init();
