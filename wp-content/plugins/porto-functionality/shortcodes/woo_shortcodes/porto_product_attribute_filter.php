<?php

// Porto Widget Woo Products
add_shortcode( 'porto_product_attribute_filter', 'porto_shortcode_product_attribute_filter' );
add_action( 'vc_after_init', 'porto_load_shortcode_product_attribute_filter' );

function porto_shortcode_product_attribute_filter( $atts, $content = null ) {
	ob_start();
	if ( $template = porto_shortcode_woo_template( 'porto_product_attribute_filter' ) ) {
		include $template;
	}
	return ob_get_clean();
}

function porto_load_shortcode_product_attribute_filter() {
	$attribute_array      = array();
	$attribute_taxonomies = wc_get_attribute_taxonomies();

	if ( ! empty( $attribute_taxonomies ) ) {
		foreach ( $attribute_taxonomies as $tax ) {
			if ( taxonomy_exists( wc_attribute_taxonomy_name( $tax->attribute_name ) ) ) {
				$attribute_array[ $tax->attribute_name ] = $tax->attribute_name;
			}
		}
	}

	$custom_class = porto_vc_custom_class();

	// woocommerce products widget
	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Product filter by attribute', 'porto-functionality' ),
			'base'        => 'porto_product_attribute_filter',
			'icon'        => 'fas fa-cart-arrow-down',
			'category'    => __( 'WooCommerce', 'porto-functionality' ),
			'description' => __( 'Display a list of attributes to filter products.', 'woocommerce' ),
			'params'      => array(
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Attribute', 'woocommerce' ),
					'param_name' => 'attribute',
					'value'      => array_merge(
						array(
							__( 'Select...', 'porto-functionality' ) => '',
						),
						$attribute_array
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Hide empty attributes', 'porto-functionality' ),
					'param_name' => 'hide_empty',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Display type', 'woocommerce' ),
					'param_name' => 'display_type',
					'value'      => array(
						__( 'Dropdown', 'woocommerce' )   => 'dropdown',
						__( 'List', 'woocommerce' )       => 'list',
						__( 'Label', 'porto-functionality' ) => 'label',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Query type', 'woocommerce' ),
					'param_name' => 'query_type',
					'value'      => array(
						__( 'AND', 'woocommerce' ) => 'and',
						__( 'OR', 'woocommerce' )  => 'or',
					),
				),
				$custom_class,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Product_Attribute_Filter' ) ) {
		class WPBakeryShortCode_Porto_Product_Attribute_Filter extends WPBakeryShortCode {
		}
	}
}
