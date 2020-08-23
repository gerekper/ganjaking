<?php

if (!function_exists('woocommerce_related_products_by_status')) {
	/**
	 * Outputs related products by a particular status.  This is the more generic version of the specialized output functions. 
	 * @global WC_Recommendation_Engine $woocommerce_recommender
	 * @param int $posts_per_page The number of products to display. 
	 * @param int $columns The number of columns to display. 
	 * @param string $activity_type The type of activity to display. Allowed values are completed and viewed. 
	 */
	function woocommerce_related_products_by_status($posts_per_page = 4, $columns = 2, $activity_type = 'completed') {
		global $woocommerce_recommender;

		$name = $activity_type == 'completed' || $activity_type == 'viewed' ? '-' . $activity_type : '';

		wc_reset_loop();
		wc_get_template('single-product/related' . $name . '.php', array(
		    'posts_per_page' => $posts_per_page,
		    'orderby' => '',
		    'columns' => $columns,
		    'activity_types' => $activity_type
		), $woocommerce_recommender->template_url . 'templates/', $woocommerce_recommender->plugin_dir() . '/templates/');
		wc_reset_loop();
	}

}

if (!function_exists('woocommerce_related_products_purchased_together')) {
	
		/**
	 * Outputs products which are frequently purchased together.  This is a generic version of the more specialized functions. 
	 * @global WC_Recommendation_Engine $woocommerce_recommender
	 * @param int $posts_per_page The number of products to display. 
	 * @param int $columns The number of columns to display. 
	 * @param string $activity_type The type of activity to display. 
	 */
	function woocommerce_related_products_purchased_together($posts_per_page = 4, $columns = 2, $activity_type = 'completed') {
		global $woocommerce_recommender;

		wc_reset_loop();
		wc_get_template('single-product/related-purchased-together.php', array(
		    'posts_per_page' => $posts_per_page,
		    'orderby' => '',
		    'columns' => $columns,
		    'activity_types' => $activity_type
		), $woocommerce_recommender->template_url . 'templates/', $woocommerce_recommender->plugin_dir() . '/templates/');
		wc_reset_loop();
	}

}

if (!function_exists('woocommerce_recommender_output_viewed_products')) {
	/**
	 * Outputs products which are related by frequent views. 
	 * 
	 * Uses the special template to load WooCommerce columns and number of products into globals for use later. 
	 */
	function woocommerce_recommender_output_viewed_products() {
		global $related_posts_per_page, $related_columns;
		woocommerce_recommender_get_posts_and_columns();

		woocommerce_related_products_by_status($related_posts_per_page, $related_columns, 'viewed');
	}

	add_shortcode('woocommerce_recommender_viewed_products', function() {
		woocommerce_recommender_output_viewed_products();
	});

}

if (!function_exists('woocommerce_recommender_output_purchased_products')) {
	/**
	 * Outputs products which are related by purchase history. 
	 * 
	 * Uses the special template to load WooCommerce columns and number of products into globals for use later. 
	 */
	function woocommerce_recommender_output_purchased_products() {
		global $related_posts_per_page, $related_columns;
		woocommerce_recommender_get_posts_and_columns();
		
		woocommerce_related_products_by_status($related_posts_per_page, $related_columns, 'completed');
	}

	add_shortcode('woocommerce_recommender_purchased_products', function() {
		woocommerce_recommender_output_purchased_products();
	});

}

if (!function_exists('woocommerce_recommender_output_purchased_together')) {
	/**
	 * Outputs products which are frequently purchased at the same time. 
	 * 
	 * Uses the special template to load WooCommerce columns and number of products into globals for use later. 
	 */

	function woocommerce_recommender_output_purchased_together() {
		global $related_posts_per_page, $related_columns;
		woocommerce_recommender_get_posts_and_columns();

		$related_posts_per_page = 3;

		woocommerce_related_products_purchased_together($related_posts_per_page, $related_columns, 'completed');
	}

	add_shortcode('woocommerce_recommender_purchased_together', function() {
		woocommerce_recommender_output_purchased_together();
	});

}




