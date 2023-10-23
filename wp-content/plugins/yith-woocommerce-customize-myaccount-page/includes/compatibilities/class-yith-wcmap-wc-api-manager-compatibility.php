<?php
/**
 * WooCommerce API Manager Compatibility Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

if ( ! class_exists( 'YITH_WCMAP_WC_Api_Manager_Compatibility' ) ) {
	/**
	 * Class YITH_WCMAP_WC_Api_Manager_Compatibility
	 *
	 * @since 3.0.0
	 */
	class YITH_WCMAP_WC_Api_Manager_Compatibility extends YITH_WCMAP_Compatibility {

		/**
		 * Constructor
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			$this->endpoint_key = array( 'api-keys', 'api-downloads' );
			$this->endpoint     = array(
				'api-keys'      => array(
					'slug'  => 'api-keys',
					'label' => __( 'API Keys', 'yith-woocommerce-customize-myaccount-page' ),
				),
				'api-downloads' => array(
					'slug'  => 'api-downloads',
					'label' => __( 'API Downloads', 'yith-woocommerce-customize-myaccount-page' ),
				),
			);

			// Register endpoint.
			$this->register_endpoint();
		}
	}
}
