<?php

/**
 * Registers the output hooks for the various recommendations based on the order defined in the settings screen. 
 * @global WC_Recommendation_Engine $woocommerce_recommender
 * @return null
 */
function woocommerce_recommender_register_hooks() {
    global $woocommerce_recommender;

    if (!is_product()) {
        return;
    }

    $wc_recommender_rbph_sort = (int) $woocommerce_recommender->get_setting('wc_recommender_rbph_sort', 21);
    $wc_recommender_rbpv_sort = (int) $woocommerce_recommender->get_setting('wc_recommender_rbpv_sort', 22);
    $wc_recommender_fpt_sort = (int) $woocommerce_recommender->get_setting('wc_recommender_fpt_sort', 23);


    if (apply_filters('woocommerce_recommender_show_recommendations_by_purchase_history', $woocommerce_recommender->get_setting('wc_recommender_rbph_enabled', 'enabled') == 'enabled', get_queried_object_id())) {
        add_action('woocommerce_after_single_product_summary', 'woocommerce_recommender_output_purchased_products', $wc_recommender_rbph_sort);
    }

    if (apply_filters('woocommerce_recommender_show_recommendations_by_product_views', $woocommerce_recommender->get_setting('wc_recommender_rbpv_enabled', 'enabled') == 'enabled', get_queried_object_id())) {
        add_action('woocommerce_after_single_product_summary', 'woocommerce_recommender_output_viewed_products', $wc_recommender_rbpv_sort);
    }

    if (apply_filters('woocommerce_recommender_show_recommendations_frequently_purchased_together', $woocommerce_recommender->get_setting('wc_recommender_fpt_enabled', 'enabled') == 'enabled', get_queried_object_id())) {
        add_action('woocommerce_after_single_product_summary', 'woocommerce_recommender_output_purchased_together', $wc_recommender_fpt_sort);
    }

    add_filter('woocommerce_locate_template', 'woocommerce_recommender_disable_related', 10, 3);
}

add_action('template_redirect', 'woocommerce_recommender_register_hooks');