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

if ( ! class_exists( 'YWSN_Metabox' ) ) {

	/**
	 * Shows Meta Box in order's details page
	 *
	 * @class   YWSN_Metabox
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWSN_Metabox {

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
			add_action( 'woocommerce_process_shop_order_meta', array( $this, 'save' ) );
			add_action( 'yith_wcfm_after_order_details', array( $this, 'frontend_output' ) );

			if ( defined( 'YITH_WCBK_VERSION' ) && version_compare( YITH_WCBK_VERSION, '2.1.9', '>=' ) ) {
				add_filter( 'yith_wcbk_booking_metaboxes_array', array( $this, 'add_metabox_booking' ) );
				add_filter( 'yith_wcbk_booking_ywsn-metabox_print', array( $this, 'output' ) );
			}

		}

		/**
		 * Add a metabox on product page
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_metabox() {

			foreach ( wc_get_order_types( 'order-meta-boxes' ) as $type ) {
				add_meta_box( 'ywsn-metabox', _x( 'SMS notifications', 'metabox title', 'yith-woocommerce-sms-notifications' ), array( $this, 'output' ), $type, 'side', 'high' );
			}

		}

		/**
		 * Add a metabox on booking page
		 *
		 * @param   $metaboxes array
		 *
		 * @return  array
		 * @since   1.4.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_metabox_booking( $metaboxes ) {

			$metaboxes[5] = array(
				'id'       => 'ywsn-metabox',
				'title'    => _x( 'SMS notifications', 'metabox title', 'yith-woocommerce-sms-notifications' ),
				'context'  => 'side',
				'priority' => 'high',
			);

			return $metaboxes;

		}

		/**
		 * Output Meta Box
		 *
		 * The function to be called to output the meta box in order/booking details page.
		 *
		 * @param   $post WP_Post
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function output( $post = null ) {

			if ( ! $post ) {
				return;
			}

			if ( 'yith_booking' === $post->post_type ) {
				$booking = yith_get_booking( $post->ID );
				$order   = $booking->get_order();
				$type    = 'booking';
			} else {
				$order = wc_get_order( $post->ID );
				$type  = 'order';
			}

			if ( ! $order ) {
				?>
				<div class="ywsn-sms-metabox">
					<?php esc_html_e( 'No SMS can be sent for this Booking.', 'yith-woocommerce-sms-notifications' ); ?>
				</div>
				<?php

				return;
			}

			$option      = $order->get_meta( '_ywsn_receive_sms' );
			$ext_charset = apply_filters( 'ywsn_additional_charsets', get_option( 'ywsn_active_charsets', array() ) );
			$sms_length  = empty( $ext_charset ) ? 160 : 70;

			if ( get_option( 'ywsn_enable_sms_length', 'no' ) === 'yes' ) {
				$sms_length = get_option( 'ywsn_sms_length', '160' );
			}

			?>
			<div class="ywsn-sms-metabox">
				<?php if ( 'requested' === get_option( 'ywsn_customer_notification' ) ) : ?>

					<fieldset>
						<legend class="screen-reader-text"><span><?php esc_html_e( 'Get order notifications via SMS.', 'yith-woocommerce-sms-notifications' ); ?></span></legend>
						<label for="_ywsn_receive_sms">
							<input name="_ywsn_receive_sms" id="_ywsn_receive_sms" type="checkbox" value="1" <?php checked( $option, 'yes' ); ?> /><?php esc_html_e( 'Get order notifications via SMS.', 'yith-woocommerce-sms-notifications' ); ?>
						</label>
					</fieldset>

				<?php endif; ?>

				<p class="ywsn-write-sms-order">
					<label><?php esc_html_e( 'Text customer:', 'yith-woocommerce-sms-notifications' ); ?>
						<textarea class="ywsn-custom-message input-text"></textarea></label>
					<span class="ywsn-char-count"><?php esc_html_e( 'Remaining characters', 'yith-woocommerce-sms-notifications' ); ?>: <span><?php echo apply_filters( 'ywsn_sms_limit', $sms_length ); ?></span></span>
				</p>
				<p>
					<button type="button" class="button-secondary ywsn-send-sms"><?php esc_html_e( 'Send', 'yith-woocommerce-sms-notifications' ); ?></button>
				</p>
				<div class="ywsn-send-result send-progress"><?php esc_html_e( 'Sending...', 'yith-woocommerce-sms-notifications' ); ?></div>
				<input type="hidden" id="YWSN_object_type" value="<?php echo $type; ?>" />
				<input type="hidden" id="YWSN_object_id" value="<?php echo $post->ID; ?>">
			</div>
			<?php

		}

		/**
		 * Save Meta Box
		 *
		 * The function to be called to save the meta box options.
		 *
		 * @return  void
		 * @since   1.0.1
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function save() {

			global $post;

			$order       = wc_get_order( $post->ID );
			$receive_sms = isset( $_POST['_ywsn_receive_sms'] ) ? 'yes' : 'no';
			$order->update_meta_data( '_ywsn_receive_sms', $receive_sms );
			$order->save();
		}

		/**
		 * Frontend Manager output
		 *
		 * @param $post WP_Post
		 *
		 * @return  void
		 * @since   1.0.1
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function frontend_output( $post ) {
			?>
			<div class="yith-wcfm-order-metabox">
				<h4><?php echo esc_html_x( 'SMS notifications', 'metabox title', 'yith-woocommerce-sms-notifications' ); ?></h4>
				<?php $this->output( $post ); ?>
			</div>
			<?php
		}

	}

	new YWSN_Metabox();

}
