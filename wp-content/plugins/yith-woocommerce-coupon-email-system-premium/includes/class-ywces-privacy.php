<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'YITH_WCES_Privacy' ) ) {
	/**
	 * Class YITH_WCES_Privacy
	 * Privacy Class
	 *
	 * @author Alberto Ruggiero
	 */
	class YITH_WCES_Privacy extends YITH_Privacy_Plugin_Abstract {

		/**
		 * YITH_WCES_Privacy constructor.
		 */
		public function __construct() {
			parent::__construct( _x( 'YITH WooCommerce Coupon Email System', 'Privacy Policy Content', 'yith-woocommerce-coupon-email-system' ) );
		}

		public function get_privacy_message( $section ) {

			$message = '';

			switch ( $section ) {
				case 'collect_and_store':
					ob_start();

					?>
                    <p class="privacy-policy-tutorial"><?php _ex( 'During the checkout process, customers can give their consent to receive coupons. When accepting, they will receive coupons according to the events/thresholds previously set by the administrator. Customers can revoke their consent from My Account section at any time.', 'Privacy Policy Content', 'yith-woocommerce-coupon-email-system' ) ?></p>
					<?php

					$message = ob_get_clean();
					break;
				case 'has_access':

					ob_start();

					?>
                    <p><?php _ex( 'Members of our team have access to the information you provide us. For example, both Administrators and Shop Managers can access to the list of all customers that want to receive coupons', 'Privacy Policy Content', 'yith-woocommerce-coupon-email-system' ) ?></p>
					<?php

					$message = ob_get_clean();
					break;

			}

			return $message;
		}
	}
}

new YITH_WCES_Privacy();