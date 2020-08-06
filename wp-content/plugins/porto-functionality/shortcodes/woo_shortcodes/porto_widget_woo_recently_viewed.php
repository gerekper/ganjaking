<?php

// Porto Widget Woo Recently Viewed
add_shortcode( 'porto_widget_woo_recently_viewed', 'porto_shortcode_widget_woo_recently_viewed' );
add_action( 'vc_after_init', 'porto_load_widget_woo_recently_viewed_shortcode' );

function porto_shortcode_widget_woo_recently_viewed( $atts, $content = null ) {
	ob_start();
	if ( $template = porto_shortcode_woo_template( 'porto_widget_woo_recently_viewed' ) ) {
		include $template;
	}
	return ob_get_clean();
}

function porto_load_widget_woo_recently_viewed_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	// woocommerce recently viewed
	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Recently Viewed', 'porto-functionality' ) . ' ' . __( 'Widget', 'porto-functionality' ),
			'base'        => 'porto_widget_woo_recently_viewed',
			'icon'        => 'fas fa-cart-arrow-down',
			'category'    => __( 'WooCommerce Widgets', 'porto-functionality' ),
			'class'       => 'wpb_vc_wp_widget',
			'description' => __( 'Display a list of recently viewed products.', 'woocommerce' ),
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

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Widget_Woo_Recently_Viewed' ) ) {
		class WPBakeryShortCode_Porto_Widget_Woo_Recently_Viewed extends WPBakeryShortCode {
		}
	}
}
