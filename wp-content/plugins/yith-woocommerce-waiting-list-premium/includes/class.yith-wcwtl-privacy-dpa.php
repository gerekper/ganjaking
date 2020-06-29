<?php
if ( ! defined( 'YITH_WCWTL' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YITH_WCWTL_Privacy_DPA' ) ) {
	/**
	 * Class YITH_WCWTL_Privacy_DPA
	 * Privacy Class
	 *
	 * @author Francesco Licandro
	 */
	class YITH_WCWTL_Privacy_DPA extends YITH_Privacy_Plugin_Abstract {

		/**
		 * YITH_YWRAQ_Privacy constructor.
		 */
		public function __construct() {
			parent::__construct( _x( 'YITH Woocommerce Waiting List Premium', 'Privacy Policy Content', 'yith-woocommerce-waiting-list' ) );
		}

		public function get_privacy_message( $section ) {
			$message = '';

			switch ( $section ) {
				case 'collect_and_store':
					$message = '<p>' . __( 'When you subscribe to a waiting list, we will track:', 'yith-woocommerce-waiting-list' ) . '</p>' .
						'<ul>' .
						'<li>' . __( 'Email address: we\'ll use this to populate a list that is used to send you notifications about subscribed product availability.', 'yith-woocommerce-waiting-list' ) . '</li>' .
						'</ul>';
					break;
				case 'has_access':
					$message = '<p>' . __( 'Members of our team have access to the information you provide us. For example, both Administrators and Shop Managers can access to your email address.', 'yith-woocommerce-waiting-list' ) . '</p>';
					break;
			}


			return $message;
		}
	}
}

new YITH_WCWTL_Privacy_DPA();