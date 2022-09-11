<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Send Email to Customer when the subscription is resumed
 *
 * @class   YITH_WC_Customer_Subscription_Resumed
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_WC_Customer_Subscription_Resumed' ) ) {
	/**
	 * YITH_WC_Customer_Subscription_Resumed
	 *
	 * @since 1.0.0
	 */
	class YITH_WC_Customer_Subscription_Resumed extends YITH_WC_Customer_Subscription {

		/**
		 * Constructor method, used to return object of the class to WC
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->id          = 'ywsbs_customer_subscription_resumed';
			$this->title       = __( 'Subscription Resumed', 'yith-woocommerce-subscription' );
			$this->description = __( 'This email is sent to the customer when subscription has been resumed', 'yith-woocommerce-subscription' );
			$this->email_type  = 'html';
			$this->heading     = __( 'Your subscription has been resumed', 'yith-woocommerce-subscription' );
			$this->subject     = __( 'Your {site_title} subscription has been resumed', 'yith-woocommerce-subscription' );

			// Call parent constructor.
			parent::__construct();

		}

		/**
		 * Get HTML content for the mail
		 *
		 * @return string HTML content of the mail
		 * @since  1.0
		 */
		public function get_content_html() {
			ob_start();
			wc_get_template(
				$this->template_html,
				array(
					'subscription'  => $this->object,
					'email_heading' => $this->heading,
					'sent_to_admin' => true,
					'plain_text'    => false,
					'email'         => $this,
				),
				'',
				$this->template_base
			);
			return ob_get_clean();
		}

	}
}

// returns instance of the mail on file include.
return new YITH_WC_Customer_Subscription_Resumed();
