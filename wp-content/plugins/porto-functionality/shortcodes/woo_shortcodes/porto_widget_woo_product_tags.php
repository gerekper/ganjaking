<?php

// Porto Widget Woo Product Tags
add_shortcode( 'porto_widget_woo_product_tags', 'porto_shortcode_widget_woo_product_tags' );
add_action( 'vc_after_init', 'porto_load_widget_woo_product_tags_shortcode' );

function porto_shortcode_widget_woo_product_tags( $atts, $content = null ) {
	ob_start();
	if ( $template = porto_shortcode_woo_template( 'porto_widget_woo_product_tags' ) ) {
		include $template;
	}
	return ob_get_clean();
}

function porto_load_widget_woo_product_tags_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	// woocommerce product tag cloud
	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Product Tags', 'porto-functionality' ) . ' ' . __( 'Widget', 'porto-functionality' ),
			'base'        => 'porto_widget_woo_product_tags',
			'icon'        => 'fas fa-cart-arrow-down',
			'category'    => __( 'WooCommerce Widgets', 'porto-functionality' ),
			'class'       => 'wpb_vc_wp_widget',
			'description' => __( 'Your most used product tags in cloud format.', 'woocommerce' ),
			'params'      => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Title', 'woocommerce' ),
					'param_name'  => 'title',
					'admin_label' => true,
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Widget_Woo_Product_Tags' ) ) {
		class WPBakeryShortCode_Porto_Widget_Woo_Product_Tags extends WPBakeryShortCode {
		}
	}
}
