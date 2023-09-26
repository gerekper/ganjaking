<?php
/**
 * WC_OD Subscription Admin.
 *
 * @package WC_OD
 * @since   1.3.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Subscription_Admin' ) ) {
	/**
	 * Class WC_OD_Subscription_Admin
	 */
	class WC_OD_Subscription_Admin {

		/**
		 * Constructor.
		 *
		 * @since 1.3.0
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
			add_action( 'woocommerce_process_shop_subscription_meta', 'WC_OD_Meta_Box_Subscription_Delivery::save', 10, 2 );
			add_action( 'wp_ajax_wc_od_refresh_subscription_delivery_meta_box', array( $this, 'refresh_delivery_meta_box' ) );
			add_action( 'woocommerce_subscription_date_updated', array( $this, 'subscription_date_updated' ), 10, 2 );
			add_action( 'wc_od_admin_subscription_delivery_preferences', 'wc_od_admin_subscription_delivery_preferences' );
		}

		/**
		 * Enqueue styles and scripts.
		 *
		 * @since 1.5.0
		 */
		public function admin_scripts() {
			if ( wc_od_get_subscription_screen_id( 'shop_subscription' ) !== wc_od_get_current_screen_id() ) {
				return;
			}

			$suffix = wc_od_get_scripts_suffix();

			wp_enqueue_style( 'wc-od-admin', WC_OD_URL . 'assets/css/wc-od-admin.css', array( 'woocommerce_admin_styles' ), WC_OD_VERSION );
			wp_enqueue_script( 'wc-od-admin-meta-boxes-subscription', WC_OD_URL . "assets/js/admin/meta-boxes-subscription{$suffix}.js", array( 'wc-admin-meta-boxes' ), WC_OD_VERSION, true );
		}

		/**
		 * Adds subscription meta boxes.
		 *
		 * @since 1.5.0
		 */
		public function add_meta_boxes() {
			$screen          = wc_od_get_subscription_screen_id( 'shop_subscription' );
			$subscription_id = wc_od_get_current_post_or_object_id( $screen );

			if ( $subscription_id && wc_od_order_is_local_pickup( $subscription_id ) ) {
				$title = __( 'Pickup details', 'woocommerce-order-delivery' );
			} else {
				$title = __( 'Delivery details', 'woocommerce-order-delivery' );
			}

			add_meta_box( 'woocommerce-subscription-delivery', $title, 'WC_OD_Meta_Box_Subscription_Delivery::output', $screen, 'side', 'core' );
		}

		/**
		 * Refreshes the 'woocommerce-subscription-delivery' meta box.
		 *
		 * @since 1.5.0
		 */
		public function refresh_delivery_meta_box() {
			ob_start();

			$subscription_id = ( ! empty( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0 ); // phpcs:ignore WordPress.Security.NonceVerification
			$subscription    = wcs_get_subscription( $subscription_id );

			WC_OD_Meta_Box_Subscription_Delivery::output( $subscription );

			$result = ob_get_clean();

			wp_send_json(
				array(
					'content' => $result,
				)
			);
		}

		/**
		 * Updates the subscription delivery date when the next payment date is modified manually by the merchant.
		 *
		 * @since 1.3.0
		 *
		 * @param WC_Subscription $subscription The subscription instance.
		 * @param string          $date_type    The date type.
		 */
		public function subscription_date_updated( $subscription, $date_type ) {
			if ( 'next_payment' !== $date_type ) {
				return;
			}

			/*
			 * Skip if the subscription is not being updated manually by the merchant.
			 * Processed in `WC_OD_Subscriptions->subscription_date_updated()`.
			 */
			if ( ! wc_od_is_save_request_for_order( $subscription->get_id() ) ) {
				return;
			}

			$delivery_date        = $subscription->get_meta( '_delivery_date' );
			$posted_delivery_date = ( isset( $_POST['_delivery_date'] ) ? wc_clean( wp_unslash( $_POST['_delivery_date'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification

			// No delivery date or modified manually by the merchant.
			if ( ! $posted_delivery_date || ( $delivery_date !== $posted_delivery_date ) ) {
				return;
			}

			// It's a valid date.
			if ( wc_od_validate_subscription_delivery_date( $subscription, $posted_delivery_date ) ) {
				return;
			}

			// Disable the save process of the meta box 'woocommerce-subscription-delivery' to avoid overwrite the value.
			remove_action( 'woocommerce_process_shop_order_meta', 'WC_OD_Meta_Box_Subscription_Delivery::save', 20 );

			// Update and store the subscription delivery date.
			wc_od_update_subscription_delivery_date( $subscription );
			wc_od_update_subscription_delivery_time_frame( $subscription );

			// $subscription doesn't have the updated delivery date, so we pass the ID to fetch the subscription object again.
			$subscription_id   = $subscription->get_id();
			$new_delivery_date = wc_od_get_order_meta( $subscription_id, '_delivery_date' );

			// Adds an internal note to the subscription to notify to the merchant.
			if ( $new_delivery_date && $posted_delivery_date !== $new_delivery_date ) {
				$delivery_details = wc_od_localize_date( $new_delivery_date );
				$time_frame_id    = wc_od_get_order_meta( $subscription_id, '_delivery_time_frame' );

				if ( $time_frame_id ) {
					$time_frame = wc_od_get_time_frame_for_date( $new_delivery_date, $time_frame_id );

					if ( $time_frame ) {
						$delivery_details .= ' [' . wc_od_time_frame_to_string( $time_frame ) . ']';
					}
				}

				wc_od_add_order_note(
					$subscription,
					sprintf( /* translators: %s: delivery details */
						__( 'Due to a change in the next payment date, the delivery details of the next order have been updated to %s.', 'woocommerce-order-delivery' ),
						"<strong>{$delivery_details}</strong>"
					)
				);
			}
		}
	}
}

return new WC_OD_Subscription_Admin();
