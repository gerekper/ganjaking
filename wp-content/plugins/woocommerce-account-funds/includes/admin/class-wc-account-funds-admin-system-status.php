<?php
/**
 * System Status Report.
 *
 * Adds extra information related to Account Funds to the system status report.
 *
 * @package WC_Account_Funds/Admin
 * @since   2.5.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Account_Funds_Admin_System_Status.
 */
class WC_Account_Funds_Admin_System_Status {

	/**
	 * Init.
	 *
	 * @since 2.5.4
	 */
	public static function init() {
		add_action( 'woocommerce_system_status_report', array( __CLASS__, 'output_content' ) );
	}

	/**
	 * Outputs the Account Funds content in the system status report.
	 *
	 * @since 2.5.4
	 */
	public static function output_content() {
		$discount_amount = get_option( 'account_funds_discount_amount' );
		$min_topup       = get_option( 'account_funds_min_topup' );
		$max_topup       = get_option( 'account_funds_max_topup' );

		$data = array(
			'name'                  => wc_get_account_funds_name(),
			'partial_funds_payment' => get_option( 'account_funds_partial_payment', 'no' ),
			'enable_topup'          => get_option( 'account_funds_enable_topup', 'no' ),
			'min_topup'             => ( ! empty( $min_topup ) ? $min_topup : '1' ),
			'max_topup'             => ( ! empty( $max_topup ) ? $max_topup : '-' ),
			'give_discount'         => get_option( 'account_funds_give_discount', 'no' ),
			'discount_type'         => get_option( 'account_funds_discount_type', 'fixed' ),
			'discount_amount'       => ( ! empty( $discount_amount ) ? $discount_amount : '-' ),
		);

		include_once dirname( __FILE__ ) . '/views/html-admin-status-report-settings.php';
	}

	/**
	 * Outputs the HTML content for the boolean value.
	 *
	 * @since 2.5.4
	 *
	 * @param bool $value The bool to format.
	 */
	public static function output_bool_html( $value ) {
		printf(
			'<mark class="%1$s"><span class="dashicons dashicons-%2$s"></span></mark>',
			esc_attr( $value ? 'yes' : 'error' ),
			esc_attr( $value ? 'yes' : 'no-alt' )
		);
	}
}

WC_Account_Funds_Admin_System_Status::init();
