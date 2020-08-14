<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAC_VERSION' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YITH_YWRAC_Privacy_DPA' ) ) {
	/**
	 * Class YITH_YWRAC_Privacy_DPA
	 * Privacy Class
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_YWRAC_Privacy_DPA extends YITH_Privacy_Plugin_Abstract {

		/**
		 * YITH_YWRAC_Privacy constructor.
		 */
		public function __construct() {
			parent::__construct( _x( 'YITH WooCommerce Recover Abandoned Cart Premium', 'Privacy Policy Content', 'yith-woocommerce-recover-abandoned-cart' ) );
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
					$message = '<p>' . __( 'While you visit our site, we\'ll track:', 'yith-woocommerce-recover-abandoned-cart' ) . '</p>' .
							   '<ul>' .
							   '<li>' . __( 'Products added to cart: these will be used to send you marketing messages and invite you to complete the order.', 'yith-woocommerce-recover-abandoned-cart' ) . '</li>' .
							   '<li>' . __( 'Name, Last name, Email and Phone number: data used with the purpose to contact you.', 'yith-woocommerce-recover-abandoned-cart' ) . '</li>' .
							   '</ul>' .
								'<p>' . __( 'We\'ll also use cookies to keep track of your cart ID while you\'re browsing our site.', 'yith-woocommerce-recover-abandoned-cart' ) . '</p>' .
							   '<p class="privacy-policy-tutorial">' . __( 'Note: you may want to provide further details about your cookie policy and add a link to that section from here.', 'yith-woocommerce-recover-abandoned-cart' ) . '</p>';
					break;
				case 'has_access':
					$message = '<p>' . __( 'Members of our team have access to the information you provide. For example, both Administrators and Shop Managers.', 'yith-woocommerce-recover-abandoned-cart' ) . '</p>' .
							   '<p>' . __( 'Our team members have access to this information to help you fulfill orders and provide support.', 'yith-woocommerce-recover-abandoned-cart' ) . '</p>';
				default;

			}

			return apply_filters( 'ywrac_privacy_policy_content', $message, $section );

		}
	}
}

new YITH_YWRAC_Privacy_DPA();
