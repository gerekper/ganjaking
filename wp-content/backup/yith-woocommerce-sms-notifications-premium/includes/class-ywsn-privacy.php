<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'YWSN_Privacy' ) ) {
	/**
	 * Class YWSN_Privacy
	 * Privacy Class
	 *
	 * @author Alberto Ruggiero
	 */
	class YWSN_Privacy extends YITH_Privacy_Plugin_Abstract {

		/**
		 * YWSN_Privacy constructor.
		 */
		public function __construct() {
			parent::__construct( _x( 'YITH WooCommerce SMS Notifications', 'Privacy Policy Content', 'yith-woocommerce-sms-notifications' ) );
		}

		public function get_privacy_message( $section ) {

			$message = '';

			switch ( $section ) {
				case 'collect_and_store':
					ob_start();

					?>
					<p class="privacy-policy-tutorial"><?php _ex( 'During the checkout, customers can accept to receive SMS texts with information about the status of their orders.', 'Privacy Policy Content', 'yith-woocommerce-sms-notifications' ); ?></p>
					<p class="privacy-policy-tutorial"><?php _ex( 'If they accept, they will be sent an SMS text every time the order status has changed (if set by the admin).', 'Privacy Policy Content', 'yith-woocommerce-sms-notifications' ); ?></p>
					<p class="privacy-policy-tutorial"><?php _ex( 'The admin will also be able to send texts during the management of the order.', 'Privacy Policy Content', 'yith-woocommerce-sms-notifications' ); ?></p>
					<p><?php _ex( 'Note: the telephone number used is the one entered by the customer in the order details. Therefore it is subject to WooCommerce plugin Privacy Policy.', 'Privacy Policy Content', 'yith-woocommerce-sms-notifications' ); ?></p>
					<?php

					$message = ob_get_clean();
					break;

			}

			return $message;
		}
	}
}

new YWSN_Privacy();
