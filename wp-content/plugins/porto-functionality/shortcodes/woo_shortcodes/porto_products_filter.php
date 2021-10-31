<?php

// Porto Widget Woo Products
add_shortcode( 'porto_products_filter', 'porto_shortcode_products_filter' );
add_action( 'vc_after_init', 'porto_load_shortcode_products_filter' );

function porto_shortcode_products_filter( $atts, $content = null ) {
	ob_start();
	if ( $template = porto_shortcode_woo_template( 'porto_products_filter' ) ) {
		include $template;
	}
	return ob_get_clean();
}

function porto_load_shortcode_products_filter() {
	$filter_areas         = array();
	$attribute_taxonomies = wc_get_attribute_taxonomies();

	if ( ! empty( $attribute_taxonomies ) ) {
		foreach ( $attribute_taxonomies as $tax ) {
			if ( taxonomy_exists( wc_attribute_taxonomy_name( $tax->attribute_name ) ) ) {
				$filter_areas[ $tax->attribute_name ] = $tax->attribute_name;
			}
		}
	}

	$custom_class = porto_vc_custom_class();

	// woocommerce products widget
	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Products filter', 'porto-functionality' ),
			'base'        => 'porto_products_filter',
			'icon'        => 'fas fa-cart-arrow-down',
			'category'    => __( 'WooCommerce', 'porto-functionality' ),
			'description' => __( 'Display a list of select boxes to filter products by category, price or attributes.', 'porto-functionality' ),
			'params'      => array(
				array(
					'type'        => 'porto_multiselect',
					'heading'     => __( 'Filter Areas', 'porto-functionality' ),
					'param_name'  => 'filter_areas',
					'std'         => '',
					'value'       => array_merge(
						array(
							__( 'Category', 'porto-functionality' ) => 'category',
							__( 'Price', 'porto-functionality' ) => 'price',
						),
						$filter_areas
					),
					'admin_label' => true,
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Filter Titles', 'porto-functionality' ),
					'description' => __( 'comma separated list of titles', 'porto-functionality' ),
					'param_name'  => 'filter_titles',
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Price Range', 'porto-functionality' ),
					'description' => __( 'Example: 0-10, 10-100, 100-200, 200-500', 'porto-functionality' ),
					'param_name'  => 'price_range',
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Price Format', 'porto-functionality' ),
					'description' => __( 'Example: $from to $to', 'porto-functionality' ),
					'param_name'  => 'price_format',
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Hide empty categories/attributes', 'porto-functionality' ),
					'param_name' => 'hide_empty',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Display type', 'woocommerce' ),
					'param_name' => 'display_type',
					'value'      => array(
						__( 'Dropdown', 'woocommerce' ) => '',
						__( 'List', 'woocommerce' )     => 'list',
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
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Submit Button Text', 'porto-functionality' ),
					'param_name' => 'submit_value',
					'dependency' => array(
						'element' => 'display_type',
						'value'   => array( '' ),
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Submit Button Class', 'porto-functionality' ),
					'param_name' => 'submit_class',
					'dependency' => array(
						'element' => 'display_type',
						'value'   => array( '' ),
					),
				),
				$custom_class,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Products_Filter' ) ) {
		class WPBakeryShortCode_Porto_Products_Filter extends WPBakeryShortCode {
		}
	}
}
