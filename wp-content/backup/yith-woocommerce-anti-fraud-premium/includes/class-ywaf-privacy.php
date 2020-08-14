<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'YITH_WAF_Privacy' ) ) {
	/**
	 * Class YITH_WAF_Privacy
	 * Privacy Class
	 *
	 * @author Alberto Ruggiero
	 */
	class YITH_WAF_Privacy extends YITH_Privacy_Plugin_Abstract {

		/**
		 * YITH_WAF_Privacy constructor.
		 */
		public function __construct() {
			parent::__construct( _x( 'YITH WooCommerce Anti-Fraud', 'Privacy Policy Content', 'yith-woocommerce-anti-fraud' ) );
		}

		public function get_privacy_message( $section ) {

			$message = '';

			switch ( $section ) {
				case 'collect_and_store':
					ob_start();

					?>
                    <p><?php _ex( 'Note: this text applies only if “Blacklist” option has been enabled.', 'Privacy Policy Content', 'yith-woocommerce-anti-fraud' ) ?></p>
                    <p class="privacy-policy-tutorial"><?php _ex( 'At the end of the payment process the plugin runs some checks on the information provided during the checkout, in order to spot any potential fraudulent purchase. If the fraud risk is high, the email address provided during the checkout will be added to the black list and any future purchase with that email will be prevented.
', 'Privacy Policy Content', 'yith-woocommerce-anti-fraud' ) ?></p>
                    <p><?php _ex( 'Note: this text applies only if “PayPal” option is enabled.', 'Privacy Policy Content', 'yith-woocommerce-anti-fraud' ) ?></p>
                    <p class="privacy-policy-tutorial"><?php _ex( 'At the end of the payment process through PayPal, an email is sent to the email address used for the payment containing the confirmation link. Once the email is confirmed, it will be added to a dedicated list of verified addresses and no other emails will be sent. If the user does not confirm the email address within a given number of days decided by the admin, a second email will be sent. If the email is not confirmed within this second term, again decided by the admin, the admin will be warned of a potential risk.', 'Privacy Policy Content', 'yith-woocommerce-anti-fraud' ) ?></p>
					<?php

					$message = ob_get_clean();
					break;
				case 'has_access':

					ob_start();

					?>
                    <p><?php _ex( 'Members of our team have access to the information you provide us. For example, both Administrators and Shop Managers can access:', 'Privacy Policy Content', 'yith-woocommerce-anti-fraud' ) ?></p>
                    <p>&bull; <?php _ex( 'the list of blocked emails', 'Privacy Policy Content', 'yith-woocommerce-anti-fraud' ) ?></p>
                    <p>&bull; <?php _ex( 'the list of verified PayPal email addresses', 'Privacy Policy Content', 'yith-woocommerce-anti-fraud' ) ?></p>
					<?php

					$message = ob_get_clean();
					break;
			}

			return $message;
		}
	}
}

new YITH_WAF_Privacy();