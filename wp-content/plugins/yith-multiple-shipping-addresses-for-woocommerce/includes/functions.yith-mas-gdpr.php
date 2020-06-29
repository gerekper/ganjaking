<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_filter( 'woocommerce_privacy_export_order_personal_data_props', 'yith_wcmas_export_order_personal_data_props', 10, 2 );
add_filter( 'woocommerce_privacy_export_order_personal_data_prop', 'yith_wcmas_export_order_personal_data_prop', 10, 3 );
add_filter( 'woocommerce_privacy_erase_order_personal_data', 'yith_wcmas_erase_order_personal_data', 10, 2 );

add_filter( 'wp_privacy_personal_data_exporters', 'yith_wcmas_register_addresses_exporter' );
add_filter( 'wp_privacy_personal_data_erasers', 'yith_wcmas_register_addresses_eraser' );

/**
 * GDPR add order_meta to the filter hook of WooCommerce to export personal order data associated with an email address.
 *
 * @since 1.0.4
 *
 * @param  array $props_to_export meta_orders.
 * @param  WC_Order $order The order object
 * @return array
 */
function yith_wcmas_export_order_personal_data_props( $props_to_export, $order ) {
	if ( yith_wcmas_order_has_multi_shipping( $order ) ) {
		$props_to_export[ 'ywcmas_shipping_addresses' ] = esc_html__( 'Shipping Addresses', 'yith-multiple-shipping-addresses-for-woocommerce' );
	}

	return $props_to_export;
}

/**
 * GDPR retrieve the value order_meta to add to the filer hook of WooCommerce to export personal order data associated with an email address.
 *
 * @since 1.0.4
 *
 * @param  string $value value of meta_order.
 * @param  string $prop meta_order
 * @param  WC_Order $order The order object
 * @return string
 */
function yith_wcmas_export_order_personal_data_prop( $value, $prop, $order ) {
	if ( yith_wcmas_order_has_multi_shipping( $order ) && 'ywcmas_shipping_addresses' === $prop ) {
		$shipping_items = $order->get_items( 'shipping' );
		foreach ( $shipping_items as $shipping_item_id => $shipping_item ) {
			$destination = $shipping_item->get_meta( 'ywcmas_shipping_destination' );
			if ( $destination ) {
				$value .= yith_wcmas_shipping_address_from_destination_array( $destination ) . '<br>';
			}
		}
	}

	return $value;
}

/**
 * GDPR erase order_metas to the filter hook of WooCommerce to erase personal order data associated with an email address.
 *
 * @since 1.0.4
 *
 * @param  boolean $erasure_enabled.
 * @param  WC_Order $order.
 * @return boolean
 */
function yith_wcmas_erase_order_personal_data( $erasure_enabled, $order ) {
	if ( $erasure_enabled && yith_wcmas_order_has_multi_shipping( $order ) ) {
		$shipping_items = $order->get_items( 'shipping' );
		foreach ( $shipping_items as $shipping_item_id => $shipping_item ) {
			$destination = $shipping_item->get_meta( 'ywcmas_shipping_destination' );
			if ( $destination ) {
				foreach ( $destination as $key => &$value ) {
					switch ( $key ) {
						case 'country':
							$destination[ $key ] = wp_privacy_anonymize_data( 'address_country', $value );
							break;
						case 'state':
							$destination[ $key ] = wp_privacy_anonymize_data( 'address_state', $value );
							break;
						case 'postcode':
							$destination[ $key ] = wp_privacy_anonymize_data( 'numeric_id', $value );
							break;
						case 'city':
							$destination[ $key ] = wp_privacy_anonymize_data( 'text', $value );
							break;
						case 'address':
							$destination[ $key ] = wp_privacy_anonymize_data( 'text', $value );
							break;
						case 'address_2':
							$destination[ $key ] = wp_privacy_anonymize_data( 'text', $value );
							break;
						case 'first_name':
							$destination[ $key ] = wp_privacy_anonymize_data( 'text', $value );
							break;
						case 'last_name':
							$destination[ $key ] = wp_privacy_anonymize_data( 'text', $value );
							break;
						case 'company':
							$destination[ $key ] = wp_privacy_anonymize_data( 'text', $value );
							break;
					}
				}
				$shipping_item->update_meta_data( 'ywcmas_shipping_destination', $destination, $shipping_item_id );
				$shipping_item->save();
			}
		}
	}

	return $erasure_enabled;
}

/**
 * Registers the personal data exporter for additional addresses.
 *
 * @since   1.0.4
 *
 * @param   $exporters
 *
 * @return  array
 * @author  Carlos Mora
 */
function yith_wcmas_register_addresses_exporter( $exporters ) {
	$exporters['yith-mas-addresses'] = array(
		'exporter_friendly_name' => esc_html__( 'Additional Addresses', 'yith-multiple-shipping-addresses-for-woocommerce' ),
		'callback'               => 'yith_wcmas_addresses_exporter',
	);

	return $exporters;
}


/**
 * Exports the user's additional addresses.
 *
 * @since   1.0.4
 *
 * @param   $email_address
 * @param   $page
 *
 * @return  array
 * @author  Carlos Mora
 */
function yith_wcmas_addresses_exporter( $email_address, $page = 1 ) {
	$user = get_user_by( 'email', $email_address );
	$addresses = yith_wcmas_get_user_custom_addresses( $user->ID );
	$data = array();

	foreach ( $addresses as $address_id => $address ) {
		$value = implode( '<br>', $address );
		$data[] = array( 'name' => $address_id, 'value' => $value );
	}

	$data_to_export[] = array(
		'group_id'    => 'yith_mas_addresses',
		'group_label' => esc_html__( 'Additional Addresses', 'yith-multiple-shipping-addresses-for-woocommerce' ),
		'item_id'     => 'user',
		'data'        => $data,
	);

	return array(
		'data' => $data_to_export,
		'done' => true,
	);
}


/**
 * Registers the personal data eraser for additional addresses.
 *
 * @since   1.0.4
 *
 * @param   $erasers
 *
 * @return  array
 * @author  Carlos Mora
 */
function yith_wcmas_register_addresses_eraser( $erasers ) {
	$erasers['ywrr-reminders'] = array(
		'eraser_friendly_name' => esc_html__( 'Additional Addresses', 'yith-multiple-shipping-addresses-for-woocommerce' ),
		'callback'             => 'yith_wcmas_addresses_eraser',
	);

	return $erasers;
}


/**
 * Erases the user's additional addresses.
 *
 * @since   1.0.4
 *
 * @param   $email_address
 * @param   $page
 *
 * @return  array
 * @author  Carlos Mora
 */
function yith_wcmas_addresses_eraser( $email_address, $page = 1 ) {
	$user = get_user_by( 'email', $email_address );
	$response = array(
		'items_removed'  => false,
		'items_retained' => false,
		'messages'       => array(),
		'done'           => false,
	);
	if ( delete_user_meta( $user->ID, 'yith_wcmas_shipping_addresses' ) ) {
		$response['done'] = true;
		$response['items_removed'] = true;
		$response['messages'][]    = esc_html__( 'Removed all additional addresses of the customer', 'yith-multiple-shipping-addresses-for-woocommerce' );
	}

	return $response;
}