<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'YITH_LC_Privacy' ) ) {
	/**
	 * Class YITH_LC_Privacy
	 * Privacy Class
	 *
	 * @author Alberto Ruggiero
	 */
	class YITH_LC_Privacy extends YITH_Privacy_Plugin_Abstract {

		/**
		 * YITH_LC_Privacy constructor.
		 */
		public function __construct() {
			parent::__construct( _x( 'YITH WooCommerce Live Chat', 'Privacy Policy Content', 'yith-live-chat' ) );
		}

		public function get_privacy_message( $section ) {

			$message = '';

			if ( defined( 'YLC_PREMIUM' ) && YLC_PREMIUM ) {

				switch ( $section ) {
					case 'collect_and_store':
						ob_start();

						?>
                        <p><?php _ex( 'Note: the plugin uses Firebase Realtime Database to which a DPO has to be assigned.', 'Privacy Policy Content', 'yith-live-chat' ) ?></p>
                        <p class="privacy-policy-tutorial"><?php _ex( 'Through the chat popup available on the website pages, users can get in touch with an operator or send an offline message.', 'Privacy Policy Content', 'yith-live-chat' ) ?></p>
                        <p class="privacy-policy-tutorial"><?php echo sprintf( _x( 'Whenever a chat conversation is started, the following data will be stored: username, email address and client IP address. They will be temporarily stored on Firebase (please, refer to %1$sFirebase Privacy Policy%2$s for more details).', 'Privacy Policy Content', 'yith-live-chat' ), '<a href="https://firebase.google.com/support/privacy/" target="_blank">', '</a>' ) ?></p>
                        <p class="privacy-policy-tutorial"><?php _ex( 'When the chat session ends, the user will be asked to evaluate the support received. After that, their data will be removed from Firebase and will be stored on the local database for verification or other purposes related to the support given via chat.', 'Privacy Policy Content', 'yith-live-chat' ) ?></p>
                        <p></p>
                        <p class="privacy-policy-tutorial"><?php _ex( 'Whenever an offline message is sent through the dedicated offline form, the following data will be stored: username, email address, message sent, operating system, web browser, browser version, customer IP address, site page from which the message is sent and customer consent to the Privacy Policy.', 'Privacy Policy Content', 'yith-live-chat' ) ?></p>
                        <p class="privacy-policy-tutorial"><?php _ex( 'These data will be stored in the local database and will be used for support purposes or pre-sales support questions.', 'Privacy Policy Content', 'yith-live-chat' ) ?></p>
						<?php

						$message = ob_get_clean();
						break;
					case 'has_access':

						ob_start();

						?>
                        <p><?php _ex( 'Members of our team have access to the information you provide us. For example, both Administrators and Chat Operators can access:', 'Privacy Policy Content', 'yith-live-chat' ) ?></p>
                        <p>&bull; <?php _ex( 'the list of offline messages', 'Privacy Policy Content', 'yith-live-chat' ) ?></p>
                        <p>&bull; <?php _ex( 'the list of chat logs', 'Privacy Policy Content', 'yith-live-chat' ) ?></p>
						<?php

						$message = ob_get_clean();
						break;
				}

			} else {

				switch ( $section ) {
					case 'collect_and_store':
						ob_start();

						?>
                        <p><?php _ex( 'Note: the plugin uses Firebase Realtime Database to which a DPO has to be assigned.', 'Privacy Policy Content', 'yith-live-chat' ) ?></p>
                        <p class="privacy-policy-tutorial"><?php _ex( 'Through the chat popup available on the website pages, users can get in touch with an operator or send an offline message.', 'Privacy Policy Content', 'yith-live-chat' ) ?></p>
                        <p class="privacy-policy-tutorial"><?php echo sprintf( _x( 'Whenever a chat conversation is started, the following data will be stored: username, email address and client IP address. They will be temporarily stored on Firebase (please, refer to %1$sFirebase Privacy Policy%2$s for more details).', 'Privacy Policy Content', 'yith-live-chat' ), '<a href="https://firebase.google.com/support/privacy/" target="_blank">', '</a>' ) ?></p>
                        <p class="privacy-policy-tutorial"><?php _ex( 'When the chat session ends all will be removed from Firebase and will not be stored anywhere.', 'Privacy Policy Content', 'yith-live-chat' ) ?></p>
						<?php

						$message = ob_get_clean();
						break;

				}

			}


			return $message;
		}
	}
}

new YITH_LC_Privacy();