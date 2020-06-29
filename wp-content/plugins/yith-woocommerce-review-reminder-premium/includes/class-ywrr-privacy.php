<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'YITH_WRR_Privacy' ) ) {
	/**
	 * Class YITH_WRR_Privacy
	 * Privacy Class
	 *
	 * @author Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	class YITH_WRR_Privacy extends YITH_Privacy_Plugin_Abstract {

		/**
		 * YITH_WRR_Privacy constructor.
		 */
		public function __construct() {
			parent::__construct( _x( 'YITH WooCommerce Review Reminder', 'Privacy Policy Content', 'yith-woocommerce-review-reminder' ) );
		}

		public function get_privacy_message( $section ) {

			$message = '';

			switch ( $section ) {
				case 'collect_and_store':
					ob_start();

					?>
                    <p class="privacy-policy-tutorial"><?php _ex( 'During the checkout, customers can express their consent to receive a review reminder about the product(s) they’ve purchased.', 'Privacy Policy Content', 'yith-woocommerce-review-reminder' ) ?></p>
                    <p class="privacy-policy-tutorial"><?php _ex( 'If they agree, an email will be scheduled and sent in the following days after the order is completed.', 'Privacy Policy Content', 'yith-woocommerce-review-reminder' ) ?></p>
                    <p class="privacy-policy-tutorial"><?php _ex( 'If they deny, the email address provided by users during the checkout and, if registered to the site, their ID, will be logged into a dedicated table in the database to prevent them from receiving any reminders.', 'Privacy Policy Content', 'yith-woocommerce-review-reminder' ) ?></p>
					<?php

					$message = ob_get_clean();
					break;
				case 'has_access':

					ob_start();

					?>
                    <p><?php _ex( 'Members of our team have access to the information you provide us. For example, both Administrators and Shop Managers can access:', 'Privacy Policy Content', 'yith-woocommerce-review-reminder' ) ?></p>
                    <p>&bull; <?php _ex( 'the list of scheduled emails', 'Privacy Policy Content', 'yith-woocommerce-review-reminder' ) ?></p>
                    <p>&bull; <?php _ex( 'the list of all email addresses that do not want to receive review reminders', 'Privacy Policy Content', 'yith-woocommerce-review-reminder' ) ?></p>
					<?php

					$message = ob_get_clean();
					break;
			}

			return $message;
		}
	}
}

new YITH_WRR_Privacy();
