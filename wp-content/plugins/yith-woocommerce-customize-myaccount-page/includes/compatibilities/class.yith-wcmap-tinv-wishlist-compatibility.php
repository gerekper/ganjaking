<?php
/**
 * Tinv Wishlist Compatibility Class
 *
 * @author  YITH
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

if ( ! class_exists( 'YITH_WCMAP_Tinv_Wishlist_Compatibility' ) ) {
	/**
	 * Class YITH_WCMAP_Tinv_Wishlist_Compatibility
	 *
	 * @since 3.0.0
	 */
	class YITH_WCMAP_Tinv_Wishlist_Compatibility extends YITH_WCMAP_Compatibility {

		/**
		 * Constructor
		 *
		 * @since 3.0.0
		 * @author Francesco Licandro
		 */
		public function __construct() {
			$this->endpoint_key = 'tinv-wishlist';
			$this->endpoint     = array(
				'slug'    => 'my-wishlist',
				'label'   => __( 'My Wishlist', 'yith-woocommerce-customize-myaccount-page' ),
				'icon'    => 'heart',
				'content' => '[ti_wishlistsview]',
			);

			// Register endpoint.
			$this->register_endpoint();
		}
	}
}

new YITH_WCMAP_Tinv_Wishlist_Compatibility();
