<?php
/**
 * YITH WooCommerce Bookings Compatibility Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 3.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

if ( ! class_exists( 'YITH_WCMAP_Bookings_Compatibility' ) ) {
	/**
	 * Class YITH_WCMAP_Bookings_Compatibility
	 *
	 * @since 3.0.0
	 */
	class YITH_WCMAP_Bookings_Compatibility extends YITH_WCMAP_Compatibility {

		/**
		 * Constructor
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			$this->endpoint_key = 'bookings';
			$this->endpoint     = array(
				'slug'  => 'bookings',
				'label' => __( 'Bookings', 'yith-woocommerce-customize-myaccount-page' ),
				'icon'  => 'times-circle',
			);

			$this->register_endpoint();
		}
	}
}
