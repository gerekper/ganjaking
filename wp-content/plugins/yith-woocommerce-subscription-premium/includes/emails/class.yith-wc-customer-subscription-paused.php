<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Send Email to Customer when the subscription is paused
 *
 * @class   YITH_WC_Customer_Subscription_Paused
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_WC_Customer_Subscription_Paused' ) ) {
	/**
	 * YITH_WC_Customer_Subscription_Paused
	 *
	 * @since 1.0.0
	 */
	class YITH_WC_Customer_Subscription_Paused extends YITH_WC_Customer_Subscription {
		/**
		 * Constructor method, used to return object of the class to WC
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->id          = 'ywsbs_customer_subscription_paused';
			$this->title       = __( 'Subscription Paused', 'yith-woocommerce-subscription' );
			$this->description = __( 'This email is sent to the customer when subscription has been paused', 'yith-woocommerce-subscription' );
			$this->email_type  = 'html';
			$this->heading     = __( 'Your subscription has been paused', 'yith-woocommerce-subscription' );
			$this->subject     = __( 'Your {site_title} subscription been paused', 'yith-woocommerce-subscription' );

			// Call parent constructor.
			parent::__construct();

		}
	}
}

// returns instance of the mail on file include.
return new YITH_WC_Customer_Subscription_Paused();
