<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCPO_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Pre_Order_Scheduling
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_Pre_Order_Scheduling' ) ) {
	/**
	 * Class YITH_Pre_Order
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 */
	class YITH_Pre_Order_Scheduling {

		/**
		 * Construct
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since 1.0
		 */
		public function __construct(){
			add_action( 'ywpo_preorder_date_notification', array( $this, 'send_pre_order_date_notification' ) );
			add_action( 'ywpo_preorder_date_end_check', array( $this, 'pre_order_date_end_check' ) );
			add_action( 'ywpo_preorder_is_for_sale_single_notification', array( $this, 'send_pre_order_is_for_sale_single_notification' ) );
		}

		public function send_pre_order_date_notification() {
			$is_checked = get_option( 'yith_wcpo_enable_pre_order_notification' );
			$num_days = get_option( 'yith_wcpo_notification_number_days' );
			if ( 'yes' == $is_checked && !empty( $num_days ) ) {
				$pre_order_notification = array();
				$args = array(
					'post_type' => array( 'product', 'product_variation' ),
					'numberposts' => - 1,
					'fields' => 'ids',
					'meta_query' => array(
						array(
							'key' => '_ywpo_preorder',
							'value' => 'yes',
							'compare' => '='
						),
						array(
							'key' => '_ywpo_preorder_notified',
							'compare' => 'NOT EXISTS'
						)
					));

				$posts = get_posts( $args );
				if ( ! empty( $posts ) ) {
					foreach ( $posts as $id ) {
						$pre_order = new YITH_Pre_Order_Product( $id );
						$timestamp =  $pre_order->get_for_sale_date_timestamp();
						// If the Pre-Order product has date, goes on.
						if ( ! empty( $timestamp ) ) {
							$notify_date = strtotime( ( sprintf( '-%d days', $num_days )), (int) $timestamp );

							if ( time() > $notify_date ) {
								$pre_order_notification[] = $pre_order;
								yit_save_prop( $pre_order->product, '_ywpo_preorder_notified', 'yes' );
							}
						}
					}
					// If it has Pre-Order products to notify, send the email
					if ( ! empty( $pre_order_notification ) ) {
						WC()->mailer();
						do_action( 'yith_ywpo_sale_date_end', $pre_order_notification );
					}
				}
			}
		}

		public function pre_order_date_end_check() {
			$auto_for_sale = get_option( 'yith_wcpo_enable_pre_order_purchasable' );
			$is_checked_notification = get_option( 'yith_wcpo_enable_pre_order_notification_for_sale' );
			$args = array(
				'post_type' => array( 'product', 'product_variation' ),
				'numberposts' => - 1,
				'fields' => 'ids',
				'meta_key'    => '_ywpo_preorder',
				'meta_value'  => 'yes'
			);
			// Get all Pre-Order ids
			$posts = get_posts( $args );
			if ( ! empty( $posts ) ) {
				foreach ( $posts as $id ) {
					$pre_order = new YITH_Pre_Order_Product( $id );
					$timestamp =  $pre_order->get_for_sale_date_timestamp();
                    // If Pre-Order date is going to end in next 12 hours it will be true.
					$is_end_next_12h = ( ! empty( $timestamp ) && time() > ( $timestamp - ( HOUR_IN_SECONDS * 12 ) ) );
                    if ( ( $is_end_next_12h && 'yes' == $auto_for_sale ) || ( $is_end_next_12h && 'yes' == $is_checked_notification ) ) {
						wp_schedule_single_event( $timestamp, 'ywpo_preorder_is_for_sale_single_notification' , array( $id ) );
					}
				}
			}
		}

		public function send_pre_order_is_for_sale_single_notification( $pre_order_id ) {
			$pre_order_product = new YITH_Pre_Order_Product( $pre_order_id );
			$auto_for_sale = get_option( 'yith_wcpo_enable_pre_order_purchasable' );
			$is_checked_notification = get_option( 'yith_wcpo_enable_pre_order_notification_for_sale' );
			if ( 'yes' == $auto_for_sale ) {
				$pre_order_product->clear_pre_order_product();
				wc_delete_product_transients( $pre_order_id );
			}
			if ( 'yes' == $is_checked_notification ) {
				$customers = YITH_Pre_Order_Edit_Product_Page_Premium::get_pre_order_customers( $pre_order_id );
				if ( ! $customers ) {
					return;
				}
				WC()->mailer();
				foreach ( $customers as $customer ) {
					do_action( 'yith_ywpo_is_for_sale', $customer, $pre_order_id );
				}
			}

		}

	}
}