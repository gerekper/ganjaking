<?php
/**
 * Multi Vendor integration.
 *
 * @package YITH\CustomOrderStatus
 */

defined( 'YITH_WCCOS' ) || exit; // Exit if accessed directly.


/**
 * Class YITH_WCCOS_Multi_Vendor_Integration
 */
class YITH_WCCOS_Multi_Vendor_Integration {

	/**
	 * Single instance of the class.
	 *
	 * @var YITH_WCCOS_Multi_Vendor_Integration
	 */
	private static $instance;

	/**
	 * Singleton implementation.
	 *
	 * @return YITH_WCCOS_Multi_Vendor_Integration
	 */
	public static function get_instance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	/**
	 * YITH_WCCOS_Multi_Vendor_Integration constructor.
	 */
	private function __construct() {
		if ( $this->is_enabled() ) {
			add_filter( 'yith_wccos_custom_email_recipients', array( $this, 'custom_email_recipients' ), 10, 6 );
			add_filter( 'yith_wccos_get_allowed_recipients', array( $this, 'filter_recipients' ), 10, 6 );
			add_filter( 'yith_wccos_email_recipients', array( $this, 'remove_admin_and_customer_recipients_for_suborder_emails' ), 10, 3 );
		}
	}

	/**
	 * Is the plugin enabled?
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return defined( 'YITH_WPV_PREMIUM' ) && defined( 'YITH_WPV_VERSION' ) && version_compare( YITH_WPV_VERSION, '1.12.0', '>=' );
	}

	/**
	 * Filter Recipients to add the Vendor one.
	 *
	 * @param array $recipients The recipients.
	 *
	 * @return array
	 */
	public function filter_recipients( $recipients ) {
		$recipients['vendor'] = __( 'Vendor', 'yith-woocommerce-custom-order-status' );

		return $recipients;
	}

	/**
	 * Remove admin and customer recipients for sub-order emails.
	 *
	 * @param array $recipients The recipients.
	 * @param int   $status_id  The status ID.
	 * @param int   $order_id   The order ID.
	 *
	 * @return array
	 */
	public function remove_admin_and_customer_recipients_for_suborder_emails( $recipients, $status_id, $order_id ) {
		if ( apply_filters( 'yith_wccos_wcmv_remove_admin_and_customer_recipients_in_emails', wp_get_post_parent_id( $order_id ), $order_id, $recipients, $status_id ) ) {
			$to_remove = array( get_option( 'admin_email' ) );
			$order     = wc_get_order( $order_id );

			if ( $order ) {
				$to_remove[] = $order->get_billing_email();
			}

			foreach ( $to_remove as $email ) {
				if ( isset( $recipients[ $email ] ) ) {
					unset( $recipients[ $email ] );
				}
			}
		}

		return $recipients;
	}

	/**
	 * Custom Email recipients.
	 *
	 * @param array  $email_recipients The email recipients.
	 * @param array  $recipients       The recipients.
	 * @param int    $status_id        The status ID.
	 * @param int    $order_id         The order ID.
	 * @param string $old_status       The previous status.
	 * @param string $new_status       The new status.
	 *
	 * @return bool[]|mixed
	 */
	public function custom_email_recipients( $email_recipients, $recipients, $status_id, $order_id, $old_status, $new_status ) {

        if ( in_array( 'vendor', $recipients, true ) && wp_get_post_parent_id( $order_id ) ) {

            $order     = wc_get_order( $order_id );
            $vendor_id = yith_wcmv_get_vendor_id_for_order( $order );

			// Handle backward compatibility with Multi Vendor < 4.0
			$vendor = function_exists( 'yith_wcmv_get_vendor' ) ? yith_wcmv_get_vendor( $vendor_id ) : yith_get_vendor( $vendor_id, 'user' );

			if ( $vendor->is_valid() ) {
				$vendor_email = $vendor->get_meta( 'store_email' );
				if ( empty( $vendor_email ) ) {
					$vendor_owner = get_user_by( 'id', absint( $vendor->get_owner() ) );
					$vendor_email = $vendor_owner instanceof WP_User ? $vendor_owner->user_email : false;
				}

				if ( $vendor_email ) {
					$email_recipients = array( $vendor_email => true );
				}
			}
		}

		return $email_recipients;
	}
}

return YITH_WCCOS_Multi_Vendor_Integration::get_instance();
