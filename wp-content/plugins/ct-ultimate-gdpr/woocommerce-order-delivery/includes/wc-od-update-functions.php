<?php
/**
 * Functions for updating data, used by the background updater.
 *
 * @package WC_OD/Functions
 * @since   1.4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add the shipping date to the not completed orders with delivery date.
 */
function wc_od_update_140_shipping_dates() {
	$orders = wc_get_orders(
		array(
			'type'           => 'shop_order',
			'status'         => array( 'wc-pending', 'wc-on-hold', 'wc-processing' ),
			'limit'          => -1,
			'delivery_query' => array(
				array(
					'key'     => '_delivery_date',
					'value'   => wc_od_get_local_date( false ),
					'compare' => '>=',
				),
				array(
					'key'     => '_shipping_date',
					'compare' => 'NOT EXISTS',
				),
			),
		)
	);

	foreach ( $orders as $order ) {
		$shipping_timestamp = wc_od_get_order_last_shipping_date( $order, 'update' );

		if ( $shipping_timestamp ) {
			$shipping_date = wc_od_localize_date( $shipping_timestamp, 'Y-m-d' );
			$order->update_meta_data( '_shipping_date', $shipping_date );
			$order->save();
		}
	}
}

/**
 * Update DB Version.
 */
function wc_od_update_140_db_version() {
	WC_OD_Install::update_db_version( '1.4.0' );
}

/**
 * Execute the migration 1.4.0 again to calculate the missing shipping dates from the renewal orders.
 */
function wc_od_update_141_shipping_dates() {
	wc_od_update_140_shipping_dates();
}

/**
 * Update DB Version.
 */
function wc_od_update_141_db_version() {
	WC_OD_Install::update_db_version( '1.4.1' );
}

/**
 * Set the boolean values of the settings to 'yes' or 'no'.
 */
function wc_od_update_150_settings_bool_values_to_string() {
	$settings = array_map( 'wc_od_maybe_prefix', array( 'shipping_days', 'delivery_days' ) );

	foreach ( $settings as $setting ) {
		$value = get_option( $setting );

		if ( $value ) {
			foreach ( $value as $key => $data ) {
				$value[ $key ]['enabled'] = wc_bool_to_string( $data['enabled'] );
			}

			update_option( $setting, $value );
		}
	}
}

/**
 * Set the boolean values of the subscription metas to 'yes' or 'no'.
 */
function wc_od_update_150_subscriptions_bool_values_to_string() {
	$orders = wc_get_orders(
		array(
			'type'           => 'shop_order',
			'limit'          => -1,
			'delivery_query' => array(
				array(
					'key'     => '_delivery_days',
					'compare' => 'EXISTS',
				),
			),
		)
	);

	foreach ( $orders as $order ) {
		$value = maybe_unserialize( $order->get_meta( '_delivery_days' ) );

		if ( $value ) {
			foreach ( $value as $key => $data ) {
				$value[ $key ]['enabled'] = wc_bool_to_string( $data['enabled'] );
			}

			$order->update_meta_data( '_delivery_days', $value );
			$order->save();
		}
	}
}

/**
 * Sync the 'delivery_days' setting with the new default values.
 *
 * @deprecated 2.0.0
 */
function wc_od_update_150_delivery_days_setting() {
	wc_deprecated_function( __FUNCTION__, '2.0.0', 'wc_od_update_200_update_settings' );

	wc_od_update_200_update_settings();
}

/**
 * Update DB Version.
 */
function wc_od_update_150_db_version() {
	WC_OD_Install::update_db_version( '1.5.0' );
}

/**
 * Renames the 'delivery_date_field' setting to 'delivery_fields_option'.
 */
function wc_od_update_160_rename_delivery_date_field_setting() {
	$setting = wc_od_maybe_prefix( 'delivery_date_field' );
	$value   = get_option( $setting );

	if ( false !== $value ) {
		add_option( wc_od_maybe_prefix( 'delivery_fields_option' ), $value );
		delete_option( $setting );
	}
}

/**
 * Update DB Version.
 */
function wc_od_update_160_db_version() {
	WC_OD_Install::update_db_version( '1.6.0' );
}

/**
 * Deletes empty delivery time frame values from the order metadata.
 */
function wc_od_update_186_delete_empty_time_frames_from_orders() {
	$orders = wc_get_orders(
		array(
			'type'           => 'shop_order',
			'limit'          => -1,
			'delivery_query' => array(
				array(
					'relation' => 'OR',
					array(
						'key'   => '_delivery_time_frame',
						'value' => '',
					),
					array(
						'key'   => '_delivery_time_frame',
						'value' => maybe_serialize(
							array(
								'time_from' => '',
								'time_to'   => '',
							)
						),
					),
				),
			),
		)
	);

	foreach ( $orders as $order ) {
		$order->delete_meta_data( '_delivery_time_frame' );
		$order->save();
	}
}

/**
 * Update DB Version.
 */
function wc_od_update_186_db_version() {
	WC_OD_Install::update_db_version( '1.8.6' );
}

/**
 * Updates the plugin settings.
 *
 * @since 1.9.5
 */
function wc_od_update_195_update_settings() {
	if ( false !== get_option( 'wc_od_checkout_text' ) ) {
		return;
	}

	$value = __( 'We will try our best to deliver your order on the specified date.', 'woocommerce-order-delivery' );

	update_option( 'wc_od_checkout_text', $value );
}

/**
 * Update DB Version.
 */
function wc_od_update_195_db_version() {
	WC_OD_Install::update_db_version( '1.9.5' );
}

/**
 * Updates the plugin settings.
 *
 * @since 2.0.0
 */
function wc_od_update_200_update_settings() {
	// The option 'auto' is no longer available.
	$option = get_option( 'wc_od_delivery_fields_option' );

	if ( 'auto' === $option ) {
		update_option( 'wc_od_delivery_fields_option', 'required' );
	}

	// Update the delivery days and time frames settings.
	$delivery_days = get_option( 'wc_od_delivery_days', array() );

	foreach ( $delivery_days as $day_id => $data ) {
		$delivery_day = wc_od_get_delivery_day( $data );
		$delivery_day->set_id( $day_id );
		$delivery_day->save();
	}
}

/**
 * Updates the delivery details for the already created subscriptions.
 *
 * @since 2.0.0
 */
function wc_od_update_200_update_subscriptions_delivery() {
	// Plugin not active.
	if ( ! WC_OD_Utils::is_plugin_active( 'woocommerce-subscriptions/woocommerce-subscriptions.php' ) ) {
		return false;
	}

	include_once WC_OD_PATH . 'includes/updates/class-wc-od-update-200-subscriptions-delivery.php';

	$instance = new WC_OD_Update_200_Subscriptions_Delivery();

	return $instance->update();
}

/**
 * Update DB Version.
 */
function wc_od_update_200_db_version() {
	WC_OD_Install::update_db_version( '2.0.0' );
}
