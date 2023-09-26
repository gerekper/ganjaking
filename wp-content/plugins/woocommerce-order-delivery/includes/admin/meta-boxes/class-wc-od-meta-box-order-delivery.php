<?php
/**
 * Order Delivery.
 *
 * Functions for displaying the order delivery meta box.
 *
 * @package WC_OD/Admin/Meta Boxes
 * @since   1.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_OD_Meta_Box_Order_Delivery
 */
class WC_OD_Meta_Box_Order_Delivery {

	/**
	 * Delivery fields.
	 *
	 * @var array
	 */
	protected static $fields = array();

	/**
	 * Init delivery fields we display + save.
	 *
	 * @since 1.5.0
	 *
	 * @param mixed $the_order Order object or ID.
	 */
	public static function init_fields( $the_order ) {
		$order = wc_od_get_order( $the_order );

		$fields = array(
			'shipping_date'       => array(
				'id'                => '_shipping_date',
				'label'             => __( 'Shipping date:', 'woocommerce-order-delivery' ),
				'type'              => 'text',
				'class'             => array( 'date-picker-field' ),
				'custom_attributes' => array(
					'maxlength' => '10',
					'pattern'   => '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])',
				),
			),
			'delivery_date'       => array(
				'id'                => '_delivery_date',
				'label'             => __( 'Delivery date:', 'woocommerce-order-delivery' ),
				'type'              => 'text',
				'class'             => array( 'date-picker-field' ),
				'custom_attributes' => array(
					'maxlength' => '10',
					'pattern'   => '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])',
				),
			),
			'delivery_time_frame' => array(
				'id'    => '_delivery_time_frame',
				'label' => __( 'Time frame:', 'woocommerce-order-delivery' ),
				'type'  => 'time_frame',
			),
		);

		if ( wc_od_order_is_local_pickup( $order ) ) {
			unset( $fields['shipping_date'] );

			$fields['delivery_date']['label'] = __( 'Pickup date:', 'woocommerce-order-delivery' );
		}

		/**
		 * Filters the order delivery fields.
		 *
		 * @since 1.5.0
		 *
		 * @param array    $fields An array with the delivery fields.
		 * @param WC_Order $order  The order instance.
		 */
		self::$fields = apply_filters( 'wc_od_admin_order_delivery_fields', $fields, $order );
	}

	/**
	 * Output the meta box.
	 *
	 * @since 1.5.0
	 * @since 2.4.0 Accepts an Order object as the first argument.
	 *
	 * @param mixed $object Order or post object.
	 */
	public static function output( $object ) {
		$order = ( $object instanceof WC_Order ? $object : wc_get_order( $object->ID ) );

		self::init_fields( $order );

		$fields = self::$fields;

		include 'views/html-order-delivery.php';
	}

	/**
	 * Save meta box data.
	 *
	 * The fields are saved before the address data (Priority 40).
	 * You should use the $_POST variable to get the updated information.
	 *
	 * The change of the order status and the email notification is done with priority 40. To attach the
	 * correct information to the emails, we must use a lower priority.
	 *
	 * @since 1.5.0
	 *
	 * @param int   $order_id Order ID.
	 * @param mixed $object   Order or post object.
	 */
	public static function save( $order_id, $object ) {
		$order = ( $object instanceof WC_Order ? $object : wc_get_order( $order_id ) );

		if ( ! $order ) {
			return;
		}

		// Process the delivery_date field.
		$posted_delivery_date  = ( isset( $_POST['_delivery_date'] ) ? wc_clean( wp_unslash( $_POST['_delivery_date'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification
		$delivery_date_changed = ( (string) $order->get_meta( '_delivery_date' ) !== $posted_delivery_date );

		if ( $posted_delivery_date ) {
			$order->update_meta_data( '_delivery_date', $posted_delivery_date );
		} else {
			$order->delete_meta_data( '_delivery_date' );
		}

		// Process delivery time frame.
		$delivery_time_frame = ( isset( $_POST['_delivery_time_frame'] ) ? wc_clean( wp_unslash( $_POST['_delivery_time_frame'] ) ) : '' ); // phpcs:ignore: WordPress.Security.NonceVerification

		if ( $posted_delivery_date && ( ! empty( $delivery_time_frame['time_from'] ) || ! empty( $delivery_time_frame['time_to'] ) ) ) {
			$order->update_meta_data( '_delivery_time_frame', $delivery_time_frame );
		} else {
			$order->delete_meta_data( '_delivery_time_frame' );
		}

		if ( wc_od_order_is_local_pickup( $order ) ) {
			$order->delete_meta_data( '_shipping_date' );
			$order->save();
			return;
		}

		// Process the shipping_date field.
		$posted_shipping_date  = ( isset( $_POST['_shipping_date'] ) ? wc_clean( wp_unslash( $_POST['_shipping_date'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification
		$shipping_date_changed = ( (string) $order->get_meta( '_shipping_date' ) !== $posted_shipping_date );

		// Shipping date not changed manually by the merchant.
		if ( ! $shipping_date_changed && $delivery_date_changed ) {
			if ( $posted_delivery_date ) {
				// This info is updated in the WC_Meta_Box_Order_Data::save() method with priority 40.
				$shipping_country = ( isset( $_POST['_shipping_country'] ) ? wc_clean( wp_unslash( $_POST['_shipping_country'] ) ) : $order->get_shipping_country() ); // phpcs:ignore WordPress.Security.NonceVerification
				$shipping_state   = ( isset( $_POST['_shipping_state'] ) ? wc_clean( wp_unslash( $_POST['_shipping_state'] ) ) : $order->get_shipping_state() ); // phpcs:ignore WordPress.Security.NonceVerification

				// Re-calculate the shipping date.
				$posted_shipping_date = wc_od_get_last_shipping_date(
					array(
						'delivery_date'               => $posted_delivery_date,
						'shipping_method'             => wc_od_get_order_shipping_method( $order ),
						'disabled_delivery_days_args' => array(
							'type'    => 'delivery',
							'country' => $shipping_country,
							'state'   => $shipping_state,
						),
					),
					'edit-order'
				);

				if ( $posted_shipping_date ) {
					$posted_shipping_date = wc_od_localize_date( $posted_shipping_date, 'Y-m-d' );

					WC_Admin_Notices::add_custom_notice( 'shipping_date_updated', __( 'The shipping date have been updated according to the new delivery date.', 'woocommerce-order-delivery' ) );
				} else {
					WC_Admin_Notices::add_custom_notice( 'shipping_date_not_found', __( "We couldn't find a shipping date according to the delivery date.", 'woocommerce-order-delivery' ) );
				}
			} else {
				// No delivery date. Remove also the shipping date.
				$posted_shipping_date = '';
			}
		}

		if ( $posted_shipping_date ) {
			$order->update_meta_data( '_shipping_date', $posted_shipping_date );
		} else {
			$order->delete_meta_data( '_shipping_date' );
		}

		$order->save();
	}
}
