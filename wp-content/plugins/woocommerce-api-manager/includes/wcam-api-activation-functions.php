<?php

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce API Manager API Activation Functions
 *
 * Note: Functions must be called using the plugins_loaded action hook.
 *
 * @package     WooCommerce API Manager/includes/API Activation Functions
 * @author      Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @version     2.5
 */

/**
 * Get all activations assigned to user_id grouped by product ID.
 *
 * Wrapper for get_activation_resources_by_user_id().
 *
 * @since 2.5
 *
 * @param int $user_id
 *
 * @return array|bool|null|object
 */
function wc_am_get_activation_resources_by_user_id( $user_id ) {
	return WC_AM_API_ACTIVATION_DATA_STORE()->get_activation_resources_by_user_id( $user_id );
}

/**
 * Get all activations assigned to order_id grouped by product ID.
 *
 * Wrapper for get_activation_resources_by_order_id().
 *
 * @since 2.5
 *
 * @param int $order_id
 *
 * @return array|bool|null|object
 */
function wc_am_get_activation_resources_by_order_id( $order_id ) {
	return WC_AM_API_ACTIVATION_DATA_STORE()->get_activation_resources_by_order_id( $order_id );
}

/**
 * Get the total number of activations for a product using a Master API Key or Product Order API Key.
 *
 * Wrapper for get_total_activations_resources_for_api_key_by_product_id().
 *
 * @since 2.5
 *
 * @param string     $api_key Master API Key or Product Order API Key
 * @param string|int $product_id
 *
 * @return int|null|string
 */
function wc_am_get_total_activations_resources_for_api_key_by_product_id( $api_key, $product_id ) {
	return WC_AM_API_ACTIVATION_DATA_STORE()->get_total_activations_resources_for_api_key_by_product_id( $api_key, $product_id );
}