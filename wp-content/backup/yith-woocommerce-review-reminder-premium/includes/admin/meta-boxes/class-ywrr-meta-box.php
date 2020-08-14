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

if ( ! class_exists( 'YWRR_Meta_Box' ) ) {

	/**
	 * Shows Meta Box in order's details page
	 *
	 * @class   YWRR_Meta_Box
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWRR_Meta_Box {

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
			if ( defined( 'YITH_WCBK_VERSION' ) && version_compare( YITH_WCBK_VERSION, '2.1.9', '>=' ) ) {
				add_filter( 'yith_wcbk_booking_metaboxes_array', array( $this, 'add_metabox_booking' ) );
				add_filter( 'yith_wcbk_booking_ywrr-metabox_print', array( $this, 'booking_output' ) );
			}
		}

		/**
		 * Add a metabox on order page
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_metabox() {

			if ( ! ywrr_vendor_check() ) {


				foreach ( wc_get_order_types( 'order-meta-boxes' ) as $type ) {
					add_meta_box( 'ywrr-metabox', esc_html__( 'Ask for a review', 'yith-woocommerce-review-reminder' ), array( $this, 'output' ), $type, 'side', 'high' );
				}


			}

		}

		/**
		 * Add a metabox on booking page
		 *
		 * @param   $metaboxes array
		 *
		 * @return  array
		 * @since   1.6.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_metabox_booking( $metaboxes ) {

			$metaboxes[5] = array(
				'id'       => 'ywrr-metabox',
				'title'    => esc_html__( 'Ask for a review', 'yith-woocommerce-review-reminder' ),
				'context'  => 'side',
				'priority' => 'high',
			);

			return $metaboxes;

		}

		/**
		 * Output Meta Box on order page
		 *
		 * The function to be called to output the meta box in order details page.
		 *
		 * @param   $post WP_Post
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function output( $post ) {

			$order          = wc_get_order( $post->ID );
			$customer_id    = $order->get_user_id();
			$customer_email = $order->get_billing_email();

			if ( ywrr_check_blocklist( $customer_id, $customer_email ) == true ) {

				$is_funds    = $order->get_meta( '_order_has_deposit' ) == 'yes';
				$is_deposits = $order->get_created_via() == 'yith_wcdp_balance_order';
				//APPLY_FILTER: ywrr_skip_renewal_orders: check if plugin should skip subscription renewal orders
				$is_renew = $order->get_meta( 'is_a_renew' ) == 'yes' && apply_filters( 'ywrr_skip_renewal_orders', true );
				//APPLY_FILTER: ywrr_can_ask_for_review: check if plugin can ask for a review
				$can_ask_review = apply_filters( 'ywrr_can_ask_for_review', true, $order );

				if ( ywrr_check_reviewable_items( $post->ID ) == 0 || $is_funds || $is_deposits || $is_renew || ! $can_ask_review ) {
					ywrr_get_noreview_message( 'no-items' );
				} else {
					ywrr_get_send_box( $post->ID, $order );
				}

			} else {
				ywrr_get_noreview_message();
			}

		}

		/**
		 * Output Meta Box on booking page
		 *
		 * The function to be called to output the meta box in booking details page.
		 *
		 * @param   $post WP_Post
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function booking_output( $post ) {

			$booking = yith_get_booking( $post->ID );

			if ( ! $booking ) {
				return;
			}

			$order = $booking->get_order();
			if ( ! $order ) {
				ywrr_get_noreview_message( 'no-booking' );

				return;
			}
			$customer_id    = $order->get_user_id();
			$customer_email = $order->get_billing_email();

			if ( ywrr_check_blocklist( $customer_id, $customer_email ) == true ) {

				$is_funds    = $order->get_meta( '_order_has_deposit' ) == 'yes';
				$is_deposits = $order->get_created_via() == 'yith_wcdp_balance_order';
				//APPLY_FILTER: ywrr_skip_renewal_orders: check if plugin should skip subscription renewal orders
				$is_renew = $order->get_meta( 'is_a_renew' ) == 'yes' && apply_filters( 'ywrr_skip_renewal_orders', true );
				//APPLY_FILTER: ywrr_can_ask_for_review: check if plugin can ask for a review
				$can_ask_review = apply_filters( 'ywrr_can_ask_for_review', true, $order );

				if ( ! ywrr_items_has_comments_opened( $booking->get_product_id() ) || ywrr_user_has_commented( $booking->get_product_id(), $customer_email ) || $is_funds || $is_deposits || $is_renew || ! $can_ask_review ) {
					ywrr_get_noreview_message( 'no-booking' );
				} else {
					ywrr_get_send_box( $post->ID, $order, $booking->get_id(), $booking->order_item_id );
				}

			} else {
				ywrr_get_noreview_message();
			}

		}

	}

	new YWRR_Meta_Box();

}