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
		$subscription  = wcs_get_subscription( $the_subscription );
		$delivery_date = wc_od_get_subscription_delivery_field_value( $the_subscription, 'delivery_date' );

		$fields = array(
			'delivery_date' => array(
				'id'                => '_delivery_date',
				'label'             => __( 'Delivery date:', 'woocommerce-order-delivery' ),
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
				array(
					'subscription'    => $subscription,
					'shipping_method' => wc_od_get_order_shipping_method( $subscription ),
				),
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
	 *
	 * @global int $thepostid The post ID.
	 *
	 * @param WP_Post $post Optional. The post instance.
	 */
	public static function output( $post = null ) {
		global $thepostid;

		$the_subscription = ( isset( $_POST['post_id'] ) ? wc_clean( wp_unslash( $_POST['post_id'] ) ) : $post->ID ); // WPCS: CSRF ok, sanitization ok.

		// Set the global variable for AJAX requests.
		$thepostid = $the_subscription;

		self::init_fields( $the_subscription );

		$fields       = self::$fields;
		$subscription = wcs_get_subscription( $the_subscription );

		include 'views/html-subscription-delivery.php';
	}

	/**
	 * Save meta box data.
	 *
	 * @since 1.5.0
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    The post instance.
	 */
	public static function save( $post_id, $post ) {
		if ( 'shop_subscription' !== $post->post_type ) {
			return;
		}

		$delivery_date = ( isset( $_POST['_delivery_date'] ) ? wc_clean( wp_unslash( $_POST['_delivery_date'] ) ) : '' ); // WPCS: CSRF ok, sanitization ok.

		if ( $delivery_date ) {
			wc_od_update_order_meta( $post_id, '_delivery_date', $delivery_date, true );
		} else {
			wc_od_delete_order_meta( $post_id, '_delivery_date', true );
		}

		if ( $delivery_date ) {
			$delivery_time_frame = ( isset( $_POST['_delivery_time_frame'] ) ? wc_clean( wp_unslash( $_POST['_delivery_time_frame'] ) ) : '' ); // WPCS: CSRF ok, sanitization ok.
			wc_od_update_order_meta( $post_id, '_delivery_time_frame', $delivery_time_frame, true );
		} else {
			wc_od_delete_order_meta( $post_id, '_delivery_time_frame', true );
		}
	}
}
