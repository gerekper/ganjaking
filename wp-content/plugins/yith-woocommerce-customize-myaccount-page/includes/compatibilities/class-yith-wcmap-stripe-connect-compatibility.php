<?php
/**
 * YITH WooCommerce Stripe Connect Compatibility Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

if ( ! class_exists( 'YITH_WCMAP_Stripe_Connect_Compatibility' ) ) {
	/**
	 * Class YITH_WCMAP_Stripe_Connect_Compatibility
	 *
	 * @since 3.0.0
	 */
	class YITH_WCMAP_Stripe_Connect_Compatibility extends YITH_WCMAP_Compatibility {

		/**
		 * Constructor
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			$this->endpoint_key = 'stripe-connect';
			$this->endpoint     = array(
				'slug'  => 'stripe-connect',
				'label' => __( 'Stripe Connect', 'yith-woocommerce-customize-myaccount-page' ),
				'icon'  => 'money',
			);

			// Register endpoint.
			$this->register_endpoint();
		}
	}
}
