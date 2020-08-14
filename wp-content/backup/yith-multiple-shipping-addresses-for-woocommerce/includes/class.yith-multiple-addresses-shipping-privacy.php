<?php

if ( ! defined( 'YITH_WCMAS_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'YITH_Multiple_Addresses_Shipping_Privacy' ) ) {

	/**
	 * Class YITH_Multiple_Addresses_Shipping_Privacy
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 * @since 1.0.4
	 */
	class YITH_Multiple_Addresses_Shipping_Privacy extends YITH_Privacy_Plugin_Abstract {

		public function __construct() {
			parent::__construct( _x( 'YITH Multiple Shipping Addresses for WooCommerce', 'Privacy Policy Content', 'yith-multiple-shipping-addresses-for-woocommerce' ) );
		}

		public function get_privacy_message( $section ) {

			$privacy_content_path = YITH_WCMAS_TEMPLATE_PATH . '/privacy/html-policy-content-' . $section . '.php';

			if ( file_exists( $privacy_content_path ) ) {

				ob_start();

				include $privacy_content_path;

				return ob_get_clean();

			}

			return '';

		}

    }
}

new YITH_Multiple_Addresses_Shipping_Privacy();