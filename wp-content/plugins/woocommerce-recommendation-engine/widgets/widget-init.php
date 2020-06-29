<?php

/**
 * Register the widgets for recommendations. 
 */
function woocommerce_recommender_register_widgets() {
	register_widget('WooCommerce_Widget_Recommended_Products');
	register_widget('WooCommerce_Widget_Purcahsed_Products');
}

add_action('widgets_init', 'woocommerce_recommender_register_widgets');

require 'widget-recommended_products.php';
require 'widget-purchased_products.php';