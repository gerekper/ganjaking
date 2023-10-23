<?php
/**
 * YITH WooCommerce Refund Requests Compatibility Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

if ( ! class_exists( 'YITH_WCMAP_Refund_Requests_Compatibility' ) ) {
	/**
	 * Class YITH_WCMAP_Refund_Requests_Compatibility
	 *
	 * @since 3.0.0
	 */
	class YITH_WCMAP_Refund_Requests_Compatibility extends YITH_WCMAP_Compatibility {

		/**
		 * Constructor
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			$this->endpoint_key = 'refund-requests';
			$this->endpoint     = array(
				'slug'    => YITH_Advanced_Refund_System_My_Account::$my_refund_requests_endpoint,
				'label'   => __( 'Refund Requests', 'yith-woocommerce-customize-myaccount-page' ),
				'icon'    => 'times-circle',
				'content' => '[ywcars_refund_requests]',
			);

			// Register endpoint.
			$this->register_endpoint();
		}
	}
}
