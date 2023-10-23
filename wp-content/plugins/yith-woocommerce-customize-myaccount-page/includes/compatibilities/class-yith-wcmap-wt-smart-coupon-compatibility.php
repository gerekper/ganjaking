<?php
/**
 * Wt Smart Coupon Compatibility Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

if ( ! class_exists( 'YITH_WCMAP_Wt_Smart_Coupon_Compatibility' ) ) {
	/**
	 * Class YITH_WCMAP_Wt_Smart_Coupon_Compatibility
	 *
	 * @since 3.0.0
	 */
	class YITH_WCMAP_Wt_Smart_Coupon_Compatibility extends YITH_WCMAP_Compatibility {

		/**
		 * Constructor
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			$this->endpoint_key = 'wt-smart-coupon';
			$this->endpoint     = array(
				'slug'  => WT_MyAccount_SmartCoupon::$endpoint,
				'label' => __( 'My Coupons', 'yith-woocommerce-customize-myaccount-page' ),
				'icon'  => 'ticket',
			);

			// Register endpoint.
			$this->register_endpoint();
		}
	}
}
