<?php
add_action( 'init', 'woocommerce_recommender_register_shortcodes' );

function woocommerce_recommender_register_shortcodes() {
	add_shortcode( 'woocommerce_related_products_by_status', 'woocommerce_shortcode_related_products_by_status' );
}


function woocommerce_shortcode_related_products_by_status( $atts ) {
	global $wpdb, $woocommerce_recommender, $related_posts_per_page, $related_columns;
	woocommerce_recommender_get_posts_and_columns();

	$args = shortcode_atts(
		array(
			'label'          => __( 'Customers also viewed these products', 'wc_recommender' ),
			'type'           => 'viewed',
			'product_id'     => false,
			'posts_per_page' => $related_posts_per_page,
			'columns'        => $related_columns,
		),
		$atts
	);

	$the_product_id = $args['product_id'];

	if ( empty( $the_product_id ) ) {
		$session_id = WC_Recommender_Compatibility::WC()->session->get_customer_id();
		$user_id    = is_user_logged_in() ? get_current_user_id() : 0;

		if ( empty( $user_id ) ) {
			$sql = $wpdb->prepare( "SELECT product_id FROM {$woocommerce_recommender->db_tbl_session_activity} WHERE session_id = %s AND activity_type = %s ORDER BY activity_date DESC", $session_id,  $args['type']  );
		} else {
			$sql = $wpdb->prepare( "SELECT product_id FROM {$woocommerce_recommender->db_tbl_session_activity} WHERE user_id = %d AND activity_type = %s ORDER BY activity_date DESC", $user_id,  $args['type'] );
		}

		$result = $wpdb->get_var( $sql );
		if ($result && !is_wp_error($result)) {
			$the_product_id = $result;
		}
	}

	$name = $args['type'] == 'completed' || $args['type'] == 'viewed' ? '-' . $args['type'] : '';

	woocommerce_reset_loop();
	wc_get_template( 'shortcodes/related' . $name . '.php', array(
		'product_to_compare' => $the_product_id,
		'label'              => $args['label'],
		'posts_per_page'     => $args['posts_per_page'],
		'orderby'            => '',
		'columns'            => $args['columns'],
		'activity_types'     => $args['type']
	), $woocommerce_recommender->template_url . 'templates/', $woocommerce_recommender->plugin_dir() . '/templates/' );
	woocommerce_reset_loop();

}
