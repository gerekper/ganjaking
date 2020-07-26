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
			add_action( 'woocommerce_process_shop_order_meta', 'WC_OD_Meta_Box_Subscription_Delivery::save', 20, 2 );

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
			if ( 'shop_subscription' !== wc_od_get_current_screen_id() ) {
				return;
			}

			$suffix = wc_od_get_scripts_suffix();

			wp_enqueue_style( 'wc-od-admin', WC_OD_URL . 'assets/css/wc-od-admin.css', array( 'woocommerce_admin_styles' ), WC_OD_VERSION );
			wp_enqueue_script( 'wc-od-admin-meta-boxes-subscription', WC_OD_URL . "assets/js/admin/meta-boxes-subscription{$suffix}.js", array(), WC_OD_VERSION, true );
		}

		/**
		 * Adds subscription meta boxes.
		 *
		 * @since 1.5.0
		 */
		public function add_meta_boxes() {
			add_meta_box( 'woocommerce-subscription-delivery', _x( 'Next order delivery', 'meta box title', 'woocommerce-order-delivery' ), 'WC_OD_Meta_Box_Subscription_Delivery::output', 'shop_subscription', 'side', 'default' );
		}

		/**
		 * Refreshes the 'woocommerce-subscription-delivery' meta box.
		 *
		 * @since 1.5.0
		 */
		public function refresh_delivery_meta_box() {
			ob_start();
			WC_OD_Meta_Box_Subscription_Delivery::output();
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
			$posted_delivery_date = ( isset( $_POST['_delivery_date'] ) ? wc_clean( wp_unslash( $_POST['_delivery_date'] ) ) : '' ); // WPCS: sanitization ok.

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

		/**
		 * Deletes the subscription delivery date when the next payment date is deleted manually by the merchant.
		 *
		 * @since 1.3.0
		 *
		 * @param WC_Subscription $subscription The subscription instance.
		 * @param string          $date_type    The date type.
		 */
		public function subscription_date_deleted( $subscription, $date_type ) {
			wc_deprecated_function( __METHOD__, '1.5.5', 'WC_OD_Subscriptions->subscription_date_deleted()' );
		}

		/**
		 * Filter the fields to display in the subscription details section.
		 *
		 * @since 1.4.0
		 * @deprecated 1.5.0 Moved fields to the subscription delivery meta box.
		 *
		 * @param array    $fields An array with the fields data.
		 * @param WC_Order $order  The order instance.
		 * @return array
		 */
		public function subscription_details_fields( $fields, $order ) {
			wc_deprecated_function( __METHOD__, '1.5.0' );

			return $fields;
		}

		/**
		 * Customizes the delivery date field label in the subscription details.
		 *
		 * @since 1.3.0
		 * @deprecated 1.4.0 Use the 'subscription_details_fields' method instead.
		 *
		 * @param string $label The field label.
		 * @return string The field label.
		 */
		public function delivery_date_field_label( $label ) {
			wc_deprecated_function( __METHOD__, '1.4.0' );

			return $label;
		}

		/**
		 * Adds the delivery preferences in the admin subscription details.
		 *
		 * @since 1.3.0
		 * @deprecated 1.5.0 Moved to the template `includes/admin/meta-boxes/views/html-subscription-delivery.php`.
		 *
		 * @param WC_Order $order The order instance.
		 */
		public function subscription_delivery_preferences( $order ) {
			wc_deprecated_function( __METHOD__, '1.5.0' );
		}
	}
}

return new WC_OD_Subscription_Admin();
