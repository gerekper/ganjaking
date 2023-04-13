<?php
/**
 * Class for handling the order refunds in the admin screens.
 *
 * @package WC_Account_Funds/Admin
 * @since   2.9.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Account_Funds_Admin_Refunds.
 */
class WC_Account_Funds_Admin_Refunds {

	/**
	 * Constructor.
	 *
	 * @since 2.9.0
	 */
	public function __construct() {
		add_filter( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'woocommerce_create_refund', array( $this, 'process_refund' ), 10, 2 );
		add_filter( 'woocommerce_order_refund_get_parent_id', array( $this, 'filter_refund_parent_id' ), 10, 2 );
	}

	/**
	 * Enqueues admin scripts.
	 *
	 * @since 2.6.0
	 */
	public function enqueue_scripts() {
		// The AJAX requests data cannot be filtered prior to WC 6.8.
		if ( version_compare( WC_VERSION, '6.8', '<' ) ) {
			return;
		}

		// It isn't the edit-order screen.
		if (
			wc_account_funds_get_current_screen_id() !== wc_account_funds_get_order_admin_screen() ||
			! isset( $_GET['action'] ) || 'edit' !== wc_clean( wp_unslash( $_GET['action'] ) ) // phpcs:ignore WordPress.Security.NonceVerification
		) {
			return;
		}

		$order = wc_get_order();

		if ( ! $order || ! $order->get_customer_id() || 'accountfunds' === $order->get_payment_method() || wc_account_funds_order_contains_deposit( $order ) ) {
			return;
		}

		$suffix        = wc_account_funds_get_scripts_suffix();
		$refund_amount = '<span class="wc-order-refund-amount">' . wc_account_funds_format_order_price( $order, 0 ) . '</span>';

		wp_enqueue_script( 'wc-account-funds-order-refund', WC_ACCOUNT_FUNDS_URL . "assets/js/admin/order-refund{$suffix}.js", array( 'jquery' ), WC_ACCOUNT_FUNDS_VERSION, true );
		wp_localize_script(
			'wc-account-funds-order-refund',
			'wc_account_funds_order_refund_params',
			array(
				'button_text' => sprintf(
					/* translators: 1: Refund amount, 2: Funds name */
					esc_html__( 'Refund %1$s via %2$s', 'woocommerce' ), // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch
					wp_kses_post( $refund_amount ),
					wc_get_account_funds_name()
				),
			)
		);
	}

	/**
	 * Processes the refund to account funds.
	 *
	 * @since 2.9.0
	 *
	 * @param WC_Order_Refund $refund Order refund object.
	 * @param array           $args   New refund arguments.
	 */
	public function process_refund( $refund, $args ) {
		// Don't refund to account funds or already refunded.
		if (
			$args['refund_payment'] || empty( $_POST['account_funds_refund'] ) || // phpcs:ignore WordPress.Security.NonceVerification
			'yes' === $refund->get_meta( 'account_funds_refunded' )
		) {
			return;
		}

		$order       = wc_get_order( $refund->get_parent_id( 'edit' ) );
		$customer_id = $order->get_customer_id();
		$amount      = $refund->get_amount();

		WC_Account_Funds_Manager::increase_user_funds( $customer_id, $amount );

		$refund->add_meta_data( 'account_funds_refunded', 'yes' );
		$refund->save();

		$order->add_order_note(
			sprintf(
				/* translators: 1: Refund amount, 2: Funds name */
				__( 'Refunded %1$s via %2$s.', 'woocommerce-account-funds' ),
				wc_account_funds_format_order_price( $order, $amount ),
				wc_get_account_funds_name()
			)
		);
	}

	/**
	 * Filters the property 'parent_id' of a refund object.
	 *
	 * There is no hook before deleting a refund.
	 *
	 * @since 2.9.0
	 *
	 * @param int             $order_id Order ID.
	 * @param WC_Order_Refund $refund   Refund object.
	 * @return int
	 */
	public function filter_refund_parent_id( $order_id, $refund ) {
		$backtrace  = wp_debug_backtrace_summary( 'WP_Hook', 0, false ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_wp_debug_backtrace_summary
		$save_index = array_search( 'WC_Abstract_Order->get_parent_id', $backtrace, true );

		if ( 'WC_AJAX::delete_refund' === $backtrace[ $save_index + 1 ] ) {
			$this->before_delete_refund( $refund );
		}

		return $order_id;
	}

	/**
	 * Processes a refund before deleting it.
	 *
	 * @since 2.9.0
	 *
	 * @param WC_Order_Refund $refund Refund object.
	 */
	public function before_delete_refund( $refund ) {
		if ( 'yes' !== $refund->get_meta( 'account_funds_refunded' ) ) {
			return;
		}

		$order       = wc_get_order( $refund->get_parent_id( 'edit' ) );
		$customer_id = $order->get_customer_id();
		$amount      = (float) $refund->get_amount();

		$funds = WC_Account_Funds_Manager::get_user_funds( $customer_id );

		if ( $funds < $amount ) {
			$order_note = sprintf(
				/* translators: 1: funds name, 2: funds amount, 3: customer ID */
				_x( 'Insufficient %1$s to remove %2$s from user #%3$s.', 'order note', 'woocommerce-account-funds' ),
				wc_get_account_funds_name(),
				wc_account_funds_format_order_price( $order, $amount ),
				$customer_id
			);
		} else {
			WC_Account_Funds_Manager::decrease_user_funds( $customer_id, $amount );

			$order_note = sprintf(
				/* translators: 1: funds amount, 2: funds name, 3: customer ID */
				_x( 'Removed %1$s of %2$s from user #%3$s', 'order note', 'woocommerce-account-funds' ),
				wc_account_funds_format_order_price( $order, $amount ),
				wc_get_account_funds_name(),
				$customer_id
			);
		}

		$order->add_order_note( $order_note );
	}
}

return new WC_Account_Funds_Admin_Refunds();
