<?php

// Porto Widget Woo Top Rated Products
add_shortcode( 'porto_widget_woo_top_rated_products', 'porto_shortcode_widget_woo_top_rated_products' );
add_action( 'vc_after_init', 'porto_load_widget_woo_top_rated_products_shortcode' );

function porto_shortcode_widget_woo_top_rated_products( $atts, $content = null ) {
	ob_start();
	if ( $template = porto_shortcode_woo_template( 'porto_widget_woo_top_rated_products' ) ) {
		include $template;
	}
	return ob_get_clean();
}

function porto_load_widget_woo_top_rated_products_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	// woocommerce top rated products widget
	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Top Rated Products', 'porto-functionality' ) . ' ' . __( 'Widget', 'porto-functionality' ),
			'base'        => 'porto_widget_woo_top_rated_products',
			'icon'        => 'fas fa-cart-arrow-down',
			'category'    => __( 'WooCommerce Widgets', 'porto-functionality' ),
			'class'       => 'wpb_vc_wp_widget',
			'description' => __( 'Display a list of your top rated products on your site.', 'woocommerce' ),
			'params'      => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Title', 'woocommerce' ),
					'param_name'  => 'title',
					'admin_label' => true,
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Number of products to show', 'woocommerce' ),
					'param_name' => 'number',
					'value'      => 5,
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Widget_Woo_Top_Rated_Products' ) ) {
		class WPBakeryShortCode_Porto_Widget_Woo_Top_Rated_Products extends WPBakeryShortCode {
		}
	}
}
