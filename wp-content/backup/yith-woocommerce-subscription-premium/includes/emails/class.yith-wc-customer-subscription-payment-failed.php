<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Send Email to Customer
 *
 * @class   YITH_WC_Customer_Subscription_Payment_Failed
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_WC_Customer_Subscription_Payment_Failed' ) ) {

	/**
	 * YITH_WC_Customer_Subscription_Payment_Failed
	 *
	 * @since 1.0.0
	 */
	class YITH_WC_Customer_Subscription_Payment_Failed extends YITH_WC_Customer_Subscription {

		/**
		 * Constructor method, used to return object of the class to WC
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			$this->id          = 'ywsbs_customer_subscription_payment_failed';
			$this->title       = __( 'Subscription Payment Failed', 'yith-woocommerce-subscription' );
			$this->description = __( 'This email is sent to the customer when a payment is failed', 'yith-woocommerce-subscription' );
			$this->email_type  = 'html';
			$this->heading     = __( 'Subscription payment failed', 'yith-woocommerce-subscription' );
			$this->subject     = __( 'Subscription payment failed', 'yith-woocommerce-subscription' );

			// Call parent constructor
			parent::__construct();

		}

		/**
		 * Method triggered to send email
		 *
		 * @param $subscription YWSBS_Subscription
		 *
		 * @return void
		 * @since  1.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function trigger( $subscription ) {

			$failed_attempts = $subscription->has_failed_attempts();
			if ( $failed_attempts['num_of_failed_attempts'] >= $failed_attempts['max_failed_attempts'] ) {
				return;
			}

			$this->recipient = $subscription->get_billing_email();

			// Check if this email type is enabled, recipient is set
			if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
				return;
			}

			$this->object = $subscription;

			$this->template_variables = array(
				'subscription'  => $this->object,
				'email_heading' => $this->get_heading(),
				'sent_to_admin' => false,
				'email'         => $this,
			);

			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content_html(), $this->get_headers(), $this->get_attachments() );
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
return new YITH_WC_Customer_Subscription_Payment_Failed();
