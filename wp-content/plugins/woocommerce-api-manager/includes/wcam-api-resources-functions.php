<?php

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce API Manager API Resource Functions
 *
 * Note: Functions must be called using the plugins_loaded action hook.
 *
 * @package     WooCommerce API Manager/includes/API Resource Functions
 * @author      Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @version     2.5
 */

/**
 * Return all API resource order item rows matching the order_id.
 *
 * Wrapper for get_all_api_resources_for_order_id().
 *
 * @since 2.5
 *
 * @param int $order_id
 *
 * @return array Returns an indexed array that contains an object that contains the API Resource data.
 * @throws \Exception
 */
function wc_am_get_all_api_resources_for_order_id( $order_id ) {
	return WC_AM_API_RESOURCE_DATA_STORE()->get_all_api_resources_for_order_id( $order_id );
}

/**
 * Return all non WooCommerce Subscription API resource order item rows matching the order_id.
 *
 * Wrapper for get_all_api_non_wc_subscription_resources_for_order_id().
 *
 * @since 2.5
 *
 * @param int $order_id
 *
 * @return array
 * @throws \Exception
 */
function wc_am_get_all_api_non_wc_subscription_resources_for_order_id( $order_id ) {
	return WC_AM_API_RESOURCE_DATA_STORE()->get_all_api_non_wc_subscription_resources_for_order_id( $order_id );
}

/**
 * Return all API resource order item rows matching the user_id.
 *
 * Wrapper for get_api_resources_for_user_id().
 *
 * @since 2.5
 *
 * @param int $user_id
 *
 * @return array
 * @throws \Exception
 */
function wc_am_get_api_resources_for_user_id( $user_id ) {
	return WC_AM_API_RESOURCE_DATA_STORE()->get_api_resources_for_user_id( $user_id );
}

/**
 * Return all API resource order item rows matching the user_id, and sort by product title.
 *
 * Wrapper for get_api_resources_for_user_id_sort_by_product_title().
 *
 * @since 2.5
 *
 * @param int $user_id
 *
 * @return array
 * @throws \Exception
 */
function wc_am_get_api_resources_for_user_id_sort_by_product_title( $user_id ) {
	return WC_AM_API_RESOURCE_DATA_STORE()->get_api_resources_for_user_id_sort_by_product_title( $user_id );
}

/**
 * Return all API resources matching the Master API Key or Product Order API Key.
 *
 * Wrapper for get_api_resources_for_master_api_key_or_product_order_api_key().
 *
 * @since 2.5
 *
 * @param string $api_key Master API Key or Product Order API Key
 *
 * @return array
 * @throws \Exception
 */
function wc_am_get_api_resources_for_master_api_key_or_product_order_api_key( $api_key ) {
	return WC_AM_API_RESOURCE_DATA_STORE()->get_api_resources_for_master_api_key_or_product_order_api_key( $api_key );
}

/**
 * Return all API resource order item rows matching the Product Order API Key and Product ID.
 *
 * Wrapper for get_api_resources_for_product_order_api_key().
 *
 * @since 2.5
 *
 * @param string     $poak Product Order API Key.
 * @param string|int $product_id
 *
 * @return array
 * @throws \Exception
 */
function wc_am_get_api_resources_for_product_order_api_key( $poak, $product_id ) {
	return WC_AM_API_RESOURCE_DATA_STORE()->get_api_resources_for_product_order_api_key( $poak, $product_id );
}

/**
 * Returns the original array with non-active API resources removed, and only resources that match the product ID (integer) provided.
 *
 * Wrapper for get_active_api_resources_for_user_id_by_product_id_int().
 *
 * @since 2.5
 *
 * @param int $user_id
 * @param int $product_id
 *
 * @return array|bool
 * @throws \Exception
 */
function wc_am_get_active_api_resources_for_user_id_by_product_id( $user_id, $product_id ) {
	return WC_AM_API_RESOURCE_DATA_STORE()->get_active_api_resources_for_user_id_by_product_id_int( $user_id, $product_id );
}

/**
 * Returns an array of active API resources.
 *
 * Wrapper for get_active_api_resources().
 *
 * @since 2.5
 *
 * @param string $api_key Master API Key, or a Product Order API Key.
 * @param int    $product_id
 *
 * @return array|bool
 * @throws \Exception
 */
function wc_am_get_all_active_api_resources_for_api_key_by_product_id( $api_key, $product_id ) {
	return WC_AM_API_RESOURCE_DATA_STORE()->get_active_api_resources( $api_key, $product_id );
}