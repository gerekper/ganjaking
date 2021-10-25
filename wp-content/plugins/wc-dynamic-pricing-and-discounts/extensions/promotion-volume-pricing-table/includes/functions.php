<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Display volume pricing table manually for specific product
 */
if (!function_exists('rp_wcdpd_display_volume_pricing_table')) {

    function rp_wcdpd_display_volume_pricing_table($product_id)
    {
        // Load product
        if ($product = wc_get_product($product_id)) {

            // Maybe display pricing table if there are any volume pricing rules configured
            RP_WCDPD_Promotion_Volume_Pricing_Table::maybe_display_pricing_table($product);
        }
    }
}
