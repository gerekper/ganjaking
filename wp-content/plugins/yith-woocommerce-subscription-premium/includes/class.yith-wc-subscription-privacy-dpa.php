<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YITH_YWSBS_Privacy_DPA' ) ) {
	/**
	 * Class YITH_YWSBS_Privacy_DPA
	 * Privacy Class
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_YWSBS_Privacy_DPA extends YITH_Privacy_Plugin_Abstract {

		/**
		 * YITH_YWSBS_Privacy constructor.
		 */
		public function __construct() {
			parent::__construct( _x( 'YITH WooCommerce Subscription Premium', 'Privacy Policy Content', 'yith-woocommerce-subscription' ) );
		}

		/**
		 * Return the message
		 *
		 * @param $section
		 *
		 * @return string
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function get_privacy_message( $section ) {
			$message = '';

			switch ( $section ) {
				case 'collect_and_store':
					$message = '<p>' . __( 'When you buy a subscription product the following information will be stored:', 'yith-woocommerce-subscription' ) . '</p>' .
							   '<ul>' .
							   '<li>' . __( 'Your name, address, email and phone number, and billing address which will be used to populate the order and the recurring order.', 'yith-woocommerce-subscription' ) . '</li>' .
							   '<li>' . __( 'Shipping address: we\'ll ask you to enter this so we can send you the current order and the recurring orders.', 'yith-woocommerce-subscription' ) . '</li>' .
							   '<li>' . __( 'Location, IP address and browser type: we\'ll use this for purposes like estimating taxes and shipping.', 'yith-woocommerce-subscription' ) . '</li>' .
							   '</ul>' .
							   '<p>' . __( 'We\'ll use this information for purposes, such as, to:', 'yith-woocommerce-subscription' ) . '</p>' .
							   '<ul>' .
							   '<li>' . __( 'Send you information about your subscription', 'yith-woocommerce-subscription' ) . '</li>' .
							   '</ul>' .
							   '<p>' . __( 'We generally store information about you for as long as we need the information for the purposes for which we collect and use it, and as long as we are not legally required to continue to keep it.', 'yith-woocommerce-subscription' ) . '</p>' .
							   '<p class="privacy-policy-tutorial">' . __( 'For example, if a subscription is cancelled, this is definitely removed after xxx months. This includes your name, email address and billing and shipping addresses of that subscription.', 'yith-woocommerce-subscription' ) . '</p>';
					break;
				case 'has_access':
					$message = '<p>' . __( 'Members of our team have access to the information you provide us. For example, both Administrators and Shop Managers can access:', 'yith-woocommerce-subscription' ) . '</p>' .
							   '<ul>' .
							   '<li>' . __( 'Subscription information like what was purchased, when it was purchased and where it should be sent, and customer information like your name, email address, and billing and shipping information.', 'yith-woocommerce-subscription' ) . '</li>' .
							   '</ul>' .
							   '<p>' . __( 'Our team members have access to this information to help fulfill orders, process refunds and support you.', 'yith-woocommerce-subscription' ) . '</p>';
				default;

			}

			return apply_filters( 'ywsbs_privacy_policy_content', $message, $section );

		}
	}
}

new YITH_YWSBS_Privacy_DPA();
