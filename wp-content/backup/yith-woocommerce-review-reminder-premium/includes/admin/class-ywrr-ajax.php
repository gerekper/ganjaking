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

if ( ! class_exists( 'YWRR_Ajax' ) ) {

	/**
	 * Implements AJAX for YWRR plugin
	 *
	 * @class   YWRR_Ajax
	 * @since   1.1.5
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWRR_Ajax {

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.1.5
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			add_action( 'wp_ajax_ywrr_send_test_mail', array( $this, 'send_test_mail' ) );
			add_action( 'wp_ajax_ywrr_add_to_blocklist', array( $this, 'add_to_blocklist_admin' ) );
			add_action( 'wp_ajax_ywrr_unsubscribe', array( $this, 'add_to_blocklist' ) );
			add_action( 'wp_ajax_nopriv_ywrr_unsubscribe', array( $this, 'add_to_blocklist' ) );

		}

		/**
		 * Send a test mail from option panel
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function send_test_mail() {

			$total_products = wp_count_posts( 'product' );

			if ( ! $total_products->publish ) {

				wp_send_json( array( 'error' => esc_html__( 'In order to send the test email, at least one product has to be published', 'yith-woocommerce-review-reminder' ) ) );

			} else {

				$args = array(
					'posts_per_page' => 2,
					'orderby'        => 'rand',
					'post_type'      => 'product'
				);

				$random_products = get_posts( $args );

				$test_items = array();

				foreach ( $random_products as $item ) {

					$test_items[ $item->ID ]['id']   = $item->ID;
					$test_items[ $item->ID ]['name'] = $item->post_title;

				}

				$days       = get_option( 'ywrr_mail_schedule_day' );
				$test_email = $_POST['email'];
				$template   = $_POST['template'];

				try {

					$mail_args = array(
						'order_id'   => 0,
						'item_list'  => $test_items,
						'days_ago'   => $days,
						'test_email' => $test_email,
						'template'   => $template,
						'type'       => 'order',
					);

					$mail_result = apply_filters( 'send_ywrr_mail', $mail_args );

					if ( ! $mail_result ) {

						wp_send_json( array( 'error' => esc_html__( 'There was an error while sending the email', 'yith-woocommerce-review-reminder' ) ) );

					} else {

						wp_send_json( array( 'success' => true, 'message' => esc_html__( 'Test email has been sent successfully!', 'yith-woocommerce-review-reminder' ) ) );

					}

				} catch ( Exception $e ) {

					wp_send_json( array( 'error' => $e->getMessage() ) );

				}

			}

		}

		/**
		 * Handles the unsubscribe form on frontend
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_to_blocklist() {

			$response = array(
				'status' => 'failure'
			);

			$customer_id    = ! empty( $_POST['user_id'] ) ? $_POST['user_id'] : 0;
			$customer_email = ! empty( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';

			if ( empty( $customer_email ) || ! is_email( $customer_email ) ) {
				wc_add_notice( esc_html__( 'Please provide a valid email address.', 'yith-woocommerce-review-reminder' ), 'error' );
			} elseif ( $customer_email !== urldecode( base64_decode( $_POST['email_hash'] ) ) ) {
				wc_add_notice( esc_html__( 'Please retype the email address as provided.', 'yith-woocommerce-review-reminder' ), 'error' );
			} else {
				if ( true == ywrr_check_blocklist( $customer_id, $customer_email ) ) {

					try {
						ywrr_add_to_blocklist( $customer_id, $customer_email );
						wc_add_notice( esc_html__( 'Unsubscribe was successful.', 'yith-woocommerce-review-reminder' ) );
						$response['status'] = 'success';
					} catch ( Exception $e ) {
						wc_add_notice( esc_html__( 'An error has occurred', 'yith-woocommerce-review-reminder' ), 'error' );
					}

				} else {
					wc_add_notice( esc_html__( 'You have already unsubscribed', 'yith-woocommerce-review-reminder' ), 'error' );
				}
			}

			ob_start();
			wc_print_notices();
			$response['messages'] = ob_get_clean();

			echo '<!--WC_START-->' . json_encode( $response ) . '<!--WC_END-->';

			exit;

		}

		/**
		 * Handles the blocklist on backend
		 *
		 * @return  void
		 * @since   1.6.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_to_blocklist_admin() {

			$user           = get_user_by( 'email', $_POST['email'] );
			$customer_id    = ( $user == null ? 0 : $user->ID );
			$customer_email = $_POST['email'];

			if ( true == ywrr_check_blocklist( $customer_id, $customer_email ) ) {

				try {
					ywrr_add_to_blocklist( $customer_id, $customer_email );
					wp_send_json( array( 'success' => true, 'message' => sprintf( esc_html__( 'User %s added successfully', 'yith-woocommerce-review-reminder' ), '<b>' . $customer_email . '</b>' ) ) );
				} catch ( Exception $e ) {
					wp_send_json( array( 'error' => $e->getMessage() ) );
				}

			} else {
				wp_send_json( array( 'error' => true, 'message' => sprintf( esc_html__( 'User %s already unsubscribed', 'yith-woocommerce-review-reminder' ), '<b>' . $customer_email . '</b>' ) ) );
			}

		}

	}

	new YWRR_Ajax();

}

