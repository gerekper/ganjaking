<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Functions
 */

/**
 * Get product pricing rules applicable to product
 *
 * @param WC_Product|int $product
 * @param int $quantity
 * @param array $variation_attributes
 * @return array
 */
function rp_wcdpd_get_product_pricing_rules_applicable_to_product($product, $quantity = 1, $variation_attributes = array())
{

    // Ready or fail
    RP_WCDPD::ready_or_fail(__FUNCTION__);

    // Load product
    $product = is_a($product, 'WC_Product') ? $product : wc_get_product($product);

    // Run product price test
    $price_data = RightPress_Product_Price_Test::run($product, $quantity, $variation_attributes, true);

    // Return applicable adjustments or empty array
    return !empty($price_data['all_changes']['rp_wcdpd']) ? $price_data['all_changes']['rp_wcdpd'] : array();
}

/**
 * Get product pricing rules applicable to cart item
 *
 * @param string $cart_item_key
 * @return array
 */
function rp_wcdpd_get_product_pricing_rules_applicable_to_cart_item($cart_item_key)
{

    // Ready or fail
    RP_WCDPD::ready_or_fail(__FUNCTION__);

    // Get cart item price data
    $price_data = RightPress_Product_Price_Cart::get_cart_item_price_changes($cart_item_key);

    // Return applicable adjustments or empty array
    return !empty($price_data['all_changes']['rp_wcdpd']) ? $price_data['all_changes']['rp_wcdpd'] : array();
}

/**
 * Get cart discount rules applicable to cart
 *
 * @return array
 */
function rp_wcdpd_get_cart_discount_rules_applicable_to_cart()
{

    // Ready or fail
    RP_WCDPD::ready_or_fail(__FUNCTION__);

    // Return applicable adjustments
    return RP_WCDPD_Controller_Methods_Cart_Discount::get_instance()->applicable_adjustments;
}

/**
 * Get checkout fee rules applicable to cart
 *
 * @return array
 */
function rp_wcdpd_get_checkout_fee_rules_applicable_to_cart()
{

    // Ready or fail
    RP_WCDPD::ready_or_fail(__FUNCTION__);

    // Return applicable adjustments
    return RP_WCDPD_Controller_Methods_Checkout_Fee::get_instance()->applicable_adjustments;
}
