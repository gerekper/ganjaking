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

if ( ! class_exists( 'YWCES_Ajax' ) ) {

	/**
	 * Implements AJAX for YWCES plugin
	 *
	 * @class   YWCES_Ajax
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWCES_Ajax {

		/**
		 * Single instance of the class
		 *
		 * @var \YWCES_Ajax
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YWCES_Ajax
		 * @since 1.0.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {

				self::$instance = new self();

			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			add_action( 'wp_ajax_ywces_send_test_mail', array( $this, 'send_test_mail' ) );
			add_action( 'wp_ajax_ywces_clear_expired_coupons', array( $this, 'clear_expired_coupons' ) );


		}

		/**
		 * Send a test mail from option panel
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function send_test_mail() {

			try {

				$current_user = wp_get_current_user();

				$products  = is_array( $_POST['products'] ) ? $_POST['products'] : explode( ',', $_POST['products'] );
				$threshold = isset( $_POST['threshold'] ) ? $_POST['threshold'] : '0';

				$args = array(
					'order_date' => current_time( 'Y-m-d' ),
					'threshold'  => (int) $threshold,
					'expense'    => (int) $threshold + 1,
					'product'    => array_shift( $products ),
					'days_ago'   => $_POST['days_elapsed']
				);

				switch ( $_POST['type'] ) {

					case 'product_purchasing':
					case 'birthday':
					case 'last_purchase':

						$coupon_code = YITH_WCES()->create_coupon( $current_user->ID, $_POST['type'], $_POST['coupon_info'] );

						break;

					default:

						$coupon_code = $_POST['coupon'];

				}

				if ( YITH_WCES()->check_if_coupon_exists( $coupon_code ) ) {

					$email_result = YWCES_Emails()->prepare_coupon_mail( $current_user->ID, $_POST['type'], $coupon_code, $args, $_POST['email'], $_POST['template'], $_POST['vendor_id'] );

					if ( ! $email_result ) {

						wp_send_json( array( 'error' => esc_html__( 'There was an error while sending the email', 'yith-woocommerce-coupon-email-system' ) ) );

					} else {

						wp_send_json( true );

					}

				} else {

					wp_send_json( array( 'error' => esc_html__( 'Coupon not valid', 'yith-woocommerce-coupon-email-system' ) ) );

				}

			} catch ( Exception $e ) {

				wp_send_json( array( 'error' => $e->getMessage() ) );

			}


		}

		/**
		 * Clear expired coupons manually
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function clear_expired_coupons() {

			$result = array(
				'success' => true,
				'message' => ''
			);

			try {

				$count = YITH_WCES()->trash_expired_coupons( true );

				$result['message'] = sprintf( _n( 'Operation completed. %d coupon trashed.', 'Operation completed. %d coupons trashed.', $count, 'yith-woocommerce-coupon-email-system' ), $count );

			} catch ( Exception $e ) {

				$result['success'] = false;
				$result['message'] = sprintf( esc_html__( 'An error occurred: %s', 'yith-woocommerce-coupon-email-system' ), $e->getMessage() );

			}

			wp_send_json( $result );

		}

	}

	/**
	 * Unique access to instance of YWCES_Ajax class
	 *
	 * @return \YWCES_Ajax
	 */
	function YWCES_Ajax() {

		return YWCES_Ajax::get_instance();

	}

	new YWCES_Ajax();

}