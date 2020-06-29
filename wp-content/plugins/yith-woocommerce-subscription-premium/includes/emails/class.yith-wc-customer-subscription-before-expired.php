<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Send Email to Customer
 *
 * @class   YITH_WC_Customer_Subscription_Before_Expired
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_WC_Customer_Subscription_Before_Expired' ) ) {

	/**
	 * YITH_WC_Customer_Subscription_Before_Expired
	 *
	 * @since 1.0.0
	 */
	class YITH_WC_Customer_Subscription_Before_Expired extends YITH_WC_Customer_Subscription {

		/**
		 * Constructor method, used to return object of the class to WC
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->id          = 'ywsbs_customer_subscription_before_expired';
			$this->title       = __( 'Subscription is going to expire', 'yith-woocommerce-subscription' );
			$this->description = __( 'This email is sent to the customer when subscription is going to expire', 'yith-woocommerce-subscription' );
			$this->email_type  = 'html';
			$this->heading     = __( 'Your subscription is going to expire', 'yith-woocommerce-subscription' );
			$this->subject     = __( 'Your subscription in {site_title} is going to expire', 'yith-woocommerce-subscription' );

			// Call parent constructor
			parent::__construct();

		}



	}

}


// returns instance of the mail on file include
return new YITH_WC_Customer_Subscription_Before_Expired();
