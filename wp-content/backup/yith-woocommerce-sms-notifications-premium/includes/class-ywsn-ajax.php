<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YWSN_Ajax' ) ) {

	/**
	 * Implements AJAX for YWSN plugin
	 *
	 * @class   YWSN_Ajax
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWSN_Ajax {

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			add_action( 'wp_ajax_ywsn_send_sms', array( $this, 'send_manual_sms' ) );

		}

		/**
		 * Send a manual SMS
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function send_manual_sms() {

			try {

				$order_note = '';
				$object     = null;

				if ( isset( $_POST['object_id'] ) ) {

					$object   = 'booking' === $_POST['object_type'] ? yith_get_booking( $_POST['object_id'] ) : wc_get_order( $_POST['object_id'] );
					$order    = 'booking' === $_POST['object_type'] ? $object->get_order() : $object;
					$phone    = $order->get_billing_phone();
					$country  = $order->get_billing_country();
					$sms_type = 'customer';
					$args     = array( 'object' => $object );

				} else {
					$phone    = $_POST['phone'];
					$country  = $_POST['country'];
					$sms_type = 'test';
					$args     = array();
				}

				$sms           = new YWSN_Messages( $args );
				$message       = ywsn_replace_placeholders( $_POST['message'], $object );
				$shop_country  = substr( get_option( 'woocommerce_default_country' ), 0, 2 );
				$order_country = '' !== $country ? $country : $shop_country;
				$sms_result    = $sms->pre_send_sms( $phone, $message, $sms_type, $order_country );

				if ( $object ) {

					ob_start();
					?>
					<li rel="<?php echo esc_attr( $sms->_order_note['id'] ); ?>" class="note system-note admin">
						<div class="note_content"> <?php echo wpautop( wptexturize( $sms->_order_note['text'] ) ); ?></div>
						<p class="meta"><a href="#" class="delete_note"><?php esc_html_e( 'Delete note', 'woocommerce' ); ?></a></p>
					</li>
					<?php
					$order_note = ob_get_clean();

				}

				$result = ( $object ? $order_note : '' );

				if ( ! $sms_result ) {
					wp_send_json(
						array(
							'success' => false,
							'error'   => esc_html__( 'An error has occurred while sending the message', 'yith-woocommerce-sms-notifications' ),
							'note'    => $result,
						)
					);
				} else {
					wp_send_json(
						array(
							'success' => true,
							'note'    => $result,
						)
					);
				}
			} catch ( Exception $e ) {
				wp_send_json(
					array(
						'success' => false,
						'error'   => $e->getMessage(),
						'note'    => '',
					)
				);
			}

		}

	}

	new YWSN_Ajax();

}
