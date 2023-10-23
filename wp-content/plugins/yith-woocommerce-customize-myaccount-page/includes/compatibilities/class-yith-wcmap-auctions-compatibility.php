<?php
/**
 * YITH WooCommerce Auctions Compatibility Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

if ( ! class_exists( 'YITH_WCMAP_Auctions_Compatibility' ) ) {
	/**
	 * Class YITH_WCMAP_Auctions_Compatibility
	 *
	 * @since 3.0.0
	 */
	class YITH_WCMAP_Auctions_Compatibility extends YITH_WCMAP_Compatibility {

		/**
		 * Constructor
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			$this->endpoint_key = YITH_Auctions_My_Auctions::$endpoint;
			$this->endpoint     = array(
				'slug'    => YITH_Auctions_My_Auctions::$endpoint,
				'label'   => __( 'Auctions', 'yith-woocommerce-customize-myaccount-page' ),
				'icon'    => 'gavel',
				'content' => '',
			);

			// Register endpoint.
			$this->register_endpoint();
		}
	}
}
