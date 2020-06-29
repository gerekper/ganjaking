<?php
if ( ! defined( 'YITH_YPOP_INIT' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_YPOP_Privacy_DPA' ) ) {
	/**
	 * Class YITH_YPOP_Privacy_DPA
	 * Privacy Class
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_YPOP_Privacy_DPA extends YITH_Privacy_Plugin_Abstract {

		/**
		 * YITH_YPOP_Privacy constructor.
		 */
		public function __construct() {
			parent::__construct( _x( 'YITH WooCommerce Popup Premium', 'Privacy Policy Content', 'yith-woocommerce-popup' ) );
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
					$message = '<p>' . __( 'We\'ll also use cookies to store what the user chooses about keeping the popup showing or not.', 'yith-woocommerce-popup' ) . '</p>' .
							   '<p class="privacy-policy-tutorial">' . __( 'Note: you may want to further detail your cookie policy, and link to that section from here.', 'yith-woocommerce-popup' ) . '</p>';
					break;
			}

			return apply_filters( 'yith_pop_privacy_policy_content', $message, $section );
		}
	}
}

new YITH_YPOP_Privacy_DPA();
