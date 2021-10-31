<?php

// Porto Widget Woo Products
if ( function_exists( 'register_block_type' ) ) {
	register_block_type(
		'porto/porto-products-widget',
		array(
			'attributes'      => array(
				'title'       => array(
					'type' => 'string',
				),
				'show'        => array(
					'type'    => 'string',
					'default' => '',
				),
				'number'      => array(
					'type'    => 'integer',
					'default' => 5,
				),
				'orderby'     => array(
					'type'    => 'string',
					'default' => 'date',
				),
				'order'       => array(
					'type'    => 'string',
					'default' => 'DESC',
				),
				'hide_free'   => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'show_hidden' => array(
					'type'    => 'boolean',
					'default' => false,
				),
			),
			'editor_script'   => 'porto_blocks',
			'render_callback' => 'porto_shortcode_widget_woo_products',
		)
	);
}

add_shortcode( 'porto_widget_woo_products', 'porto_shortcode_widget_woo_products' );
add_action( 'vc_after_init', 'porto_load_widget_woo_products_shortcode' );

function porto_shortcode_widget_woo_products( $atts, $content = null ) {
	ob_start();
	if ( isset( $atts['show'] ) && 'recent_view' == $atts['show'] ) {
		if ( $template = porto_shortcode_woo_template( 'porto_widget_woo_recently_viewed' ) ) {
			include $template;
		}
	} elseif ( isset( $atts['show'] ) && 'top_rated' == $atts['show'] ) {
		if ( $template = porto_shortcode_woo_template( 'porto_widget_woo_top_rated_products' ) ) {
			include $template;
		}
	} elseif ( $template = porto_shortcode_woo_template( 'porto_widget_woo_products' ) ) {
		include $template;
	}
	return ob_get_clean();
}

function porto_load_widget_woo_products_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	global $porto_settings;
	$status_values = array(
		__( 'All products', 'woocommerce' )      => '',
		__( 'Featured products', 'woocommerce' ) => 'featured',
		__( 'On-sale products', 'woocommerce' )  => 'onsale',
	);
	if ( ! empty( $porto_settings['woo-pre-order'] ) ) {
		$status_values[ __( 'Pre-Order', 'porto-functionality' ) ] = 'pre-order';
	}

	// woocommerce products widget
	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Products', 'porto-functionality' ) . ' ' . __( 'Widget', 'porto-functionality' ),
			'base'        => 'porto_widget_woo_products',
			'icon'        => 'fas fa-cart-arrow-down',
			'category'    => __( 'WooCommerce Widgets', 'porto-functionality' ),
			'class'       => 'wpb_vc_wp_widget',
			'description' => __( 'Display a list of your products on your site.', 'porto-functionality' ),
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
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Show', 'woocommerce' ),
					'param_name'  => 'show',
					'value'       => $status_values,
					'admin_label' => true,
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Order by', 'woocommerce' ),
					'param_name'  => 'orderby',
					'value'       => array(
						__( 'Date', 'woocommerce' )   => 'date',
						__( 'Price', 'woocommerce' )  => 'price',
						__( 'Random', 'woocommerce' ) => 'rand',
						__( 'Sales', 'woocommerce' )  => 'sales',
					),
					'admin_label' => true,
				),
				array(
					'type'       => 'dropdown',
					'heading'    => _x( 'Order', 'Sorting order', 'woocommerce' ),
					'param_name' => 'order',
					'value'      => array(
						__( 'DESC', 'woocommerce' ) => 'desc',
						__( 'ASC', 'woocommerce' )  => 'asc',
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Hide free products', 'woocommerce' ),
					'param_name' => 'hide_free',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show hidden products', 'woocommerce' ),
					'param_name' => 'show_hidden',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Widget_Woo_Products' ) ) {
		class WPBakeryShortCode_Porto_Widget_Woo_Products extends WPBakeryShortCode {
		}
	}
}
