<?php
/**
 * YITH WooCommerce Payouts Compatibility Class
 *
 * @author  YITH
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

if ( ! class_exists( 'YITH_WCMAP_Payouts_Compatibility' ) ) {
	/**
	 * Class YITH_WCMAP_Payouts_Compatibility
	 *
	 * @since 3.0.0
	 */
	class YITH_WCMAP_Payouts_Compatibility extends YITH_WCMAP_Compatibility {

		/**
		 * Constructor
		 *
		 * @since 3.0.0
		 * @author Francesco Licandro
		 */
		public function __construct() {
			$this->endpoint_key = 'payouts';
			$this->endpoint     = array(
				'slug'  => 'payouts',
				'label' => __( 'Payouts', 'yith-woocommerce-customize-myaccount-page' ),
				'icon'  => 'money',
			);

			// Register endpoint.
			$this->register_endpoint();
		}
	}
}

new YITH_WCMAP_Payouts_Compatibility();
