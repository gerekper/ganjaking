<?php
/**
 * Subscription Delivery.
 *
 * Functions for displaying the subscription delivery meta box.
 *
 * @package WC_OD/Admin/Meta Boxes
 * @since   1.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_OD_Meta_Box_Subscription_Delivery
 */
class WC_OD_Meta_Box_Subscription_Delivery {

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
	 * @param mixed $the_subscription Post object or post ID of the subscription.
	 */
	public static function init_fields( $the_subscription ) {
		$subscription    = wcs_get_subscription( $the_subscription );
		$shipping_method = wc_od_get_order_shipping_method( $subscription );
		$delivery_date   = wc_od_get_subscription_delivery_field_value( $subscription, 'delivery_date' );

		if ( wc_od_shipping_method_is_local_pickup( $shipping_method ) ) {
			$date_field_label = __( 'Pickup date:', 'woocommerce-order-delivery' );
		} else {
			$date_field_label = __( 'Delivery date:', 'woocommerce-order-delivery' );
		}

		$fields = array(
			'delivery_date' => array(
				'id'                => '_delivery_date',
				'label'             => $date_field_label,
				'type'              => 'text',
				'value'             => $delivery_date,
				'custom_attributes' => array(
					'maxlength' => '10',
					'pattern'   => '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])',
				),
			),
		);

		if ( $delivery_date ) {
			$choices = wc_od_get_time_frames_choices_for_date(
				$delivery_date,
				compact( 'subscription', 'shipping_method' ),
				'subscription'
			);

			if ( ! empty( $choices ) ) {
				$time_frame = wc_od_get_subscription_delivery_field_value( $the_subscription, 'delivery_time_frame' );

				$fields['delivery_time_frame'] = array(
					'id'      => '_delivery_time_frame',
					'label'   => __( 'Time frame:', 'woocommerce-order-delivery' ),
					'type'    => 'select',
					'options' => $choices,
					'value'   => $time_frame,
				);
			}
		}

		/**
		 * Filters the subscription delivery fields.
		 *
		 * @since 1.5.0
		 *
		 * @param array           $fields       An array with the delivery fields.
		 * @param WC_Subscription $subscription The subscription instance.
		 */
		self::$fields = apply_filters( 'wc_od_admin_subscription_delivery_fields', $fields, $subscription );
	}

	/**
	 * Output the meta box.
	 *
	 * @since 1.5.0
	 * @since 2.5.0 The first argument is required and accepts a subscription object.
	 *
	 * @param mixed $object Subscription or post object.
	 */
	public static function output( $object ) {
		$subscription = ( $object instanceof WC_Subscription ? $object : wcs_get_subscription( $object->ID ) );

		self::init_fields( $subscription );

		$fields = self::$fields;

		include 'views/html-subscription-delivery.php';
	}

	/**
	 * Save meta box data.
	 *
	 * @since 1.5.0
	 * @since 2.5.0 The second argument always is a subscription object.
	 *
	 * @param int             $subscription_id Subscription ID.
	 * @param WC_Subscription $subscription    Subscription object.
	 */
	public static function save( $subscription_id, $subscription ) {
		$delivery_date = ( isset( $_POST['_delivery_date'] ) ? wc_clean( wp_unslash( $_POST['_delivery_date'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification

		if ( $delivery_date ) {
			$subscription->update_meta_data( '_delivery_date', $delivery_date );
		} else {
			$subscription->delete_meta_data( '_delivery_date' );
		}

		$delivery_time_frame = ( isset( $_POST['_delivery_time_frame'] ) ? wc_clean( wp_unslash( $_POST['_delivery_time_frame'] ) ) : '' ); // phpcs:ignore: WordPress.Security.NonceVerification

		if ( $delivery_date && ! empty( $delivery_time_frame ) ) {
			$subscription->update_meta_data( '_delivery_time_frame', $delivery_time_frame );
		} else {
			$subscription->delete_meta_data( '_delivery_time_frame' );
		}

		$subscription->save();
	}
}
