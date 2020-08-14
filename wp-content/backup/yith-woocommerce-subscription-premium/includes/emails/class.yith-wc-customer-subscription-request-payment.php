<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Send Email to Customer
 *
 * @class   YITH_WC_Customer_Subscription_Request_Payment
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */
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

			// Call parent constructor
			parent::__construct();

		}

		/**
		 * Method triggered to send email
		 *
		 * @param int $subscription
		 *
		 * @return void
		 * @since  1.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function trigger( $subscription ) {

			$this->recipient = $subscription->get_billing_email();

			// Check if this email type is enabled, recipient is set
			if ( ! $this->is_enabled() || ! $this->get_recipient() || $subscription->renew_order == 0 ) {
				return;
			}

			$order = wc_get_order( $subscription->renew_order );

			if ( ! $order ) {
				return;
			}

			$this->object = $subscription;
			$this->order  = $order;

			if ( version_compare( WC()->version, '3.2.0', '<' ) ) {
				// Replace macros
				$this->find['order-number']    = '{order_number}';
				$this->replace['order-number'] = $order->get_order_number();
			} else {
				$this->placeholders['{order_number}'] = $order->get_order_number();
			}

			// Get next action and next action date
			if ( YITH_WC_Subscription()->suspension_time() ) {
				$next_activity = __( 'suspended', 'yith-woocommerce-subscription' );
			} else {
				$next_activity = __( 'cancelled', 'yith-woocommerce-subscription' );
			}

			$next_activity_date = $subscription->payment_due_date + YITH_WC_Subscription()->overdue_time();

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
		 * @return string HTML content of the mail
		 * @since  1.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function get_content_html() {
			ob_start();
			wc_get_template( $this->template_html, $this->template_variables, '', $this->template_base );
			return ob_get_clean();
		}


	}
}


// returns instance of the mail on file include
return new YITH_WC_Customer_Subscription_Request_Payment();
