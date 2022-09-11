<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Send Email to Customer to request the payment.
 *
 * @class   YITH_WC_Customer_Subscription_Request_Payment
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_WC_Customer_Subscription_Request_Payment' ) ) {
	/**
	 * YITH_WC_Customer_Subscription_Request_Payment
	 *
	 * @since 1.0.0
	 */
	class YITH_WC_Customer_Subscription_Request_Payment extends YITH_WC_Customer_Subscription {
		/**
		 * Constructor method, used to return object of the class to WC
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->id          = 'ywsbs_customer_subscription_request_payment';
			$this->title       = __( 'Subscription Payment Request', 'yith-woocommerce-subscription' );
			$this->description = __( 'This email is sent to the customer when they fail to pay within Due date', 'yith-woocommerce-subscription' );
			$this->email_type  = 'html';
			$this->heading     = __( 'Overdue payment', 'yith-woocommerce-subscription' );
			$this->subject     = __( 'Payment for order renewal {order_number} is overdue', 'yith-woocommerce-subscription' );

			// Call parent constructor.
			parent::__construct();
		}

		/**
		 * Method triggered to send email
		 *
		 * @param YWSBS_Subscription $subscription Subscription.
		 *
		 * @return void
		 * @since  1.0
		 */
		public function trigger( $subscription ) {

			$this->recipient = $subscription->get_billing_email();

			if ( 'yes' === $this->send_to_admin ) {
				$this->recipient .= ',' . get_option( 'admin_email' );
			}

			// Check if this email type is enabled, recipient is set.
			if ( ! $this->is_enabled() || ! $this->get_recipient() || $subscription->get_renew_order_id() === 0 ) {
				return;
			}

			$order = $subscription->get_renew_order();

			if ( ! $order ) {
				return;
			}

			$this->object = $subscription;
			$this->order  = $order;

			if ( version_compare( WC()->version, '3.2.0', '<' ) ) {
				// Replace macros.
				$this->find['order-number']    = '{order_number}';
				$this->replace['order-number'] = $order->get_order_number();
			} else {
				$this->placeholders['{order_number}'] = $order->get_order_number();
			}

			// Get next action and next action date.
			$next_activity      = ywsbs_get_suspension_time() ? __( 'suspended', 'yith-woocommerce-subscription' ) : __( 'cancelled', 'yith-woocommerce-subscription' );
			$next_activity_date = $subscription->get_payment_due_date() + ywsbs_get_overdue_time();

			$this->template_variables = array(
				'subscription'       => $this->object,
				'order'              => $this->order,
				'email_heading'      => $this->get_heading(),
				'sent_to_admin'      => false,
				'next_activity'      => $next_activity,
				'next_activity_date' => $next_activity_date,
				'email'              => $this,
			);

			$return = $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content_html(), $this->get_headers(), $this->get_attachments() );
		}

		/**
		 * Get HTML content for the mail
		 *
		 * @return string HTML content of the mail.
		 * @since  1.0
		 */
		public function get_content_html() {
			ob_start();
			wc_get_template( $this->template_html, $this->template_variables, '', $this->template_base );
			return ob_get_clean();
		}
	}
}


// returns instance of the mail on file include.
return new YITH_WC_Customer_Subscription_Request_Payment();
