<?php

// Porto Product Attribute
add_shortcode( 'porto_product_attribute', 'porto_shortcode_product_attribute' );
add_action( 'vc_after_init', 'porto_load_product_attribute_shortcode' );

function porto_shortcode_product_attribute( $atts, $content = null ) {
	ob_start();
	if ( $template = porto_shortcode_woo_template( 'porto_products' ) ) {
		if ( ! is_array( $atts ) ) {
			$atts = array();
		}
		$atts['shortcode'] = 'product_attribute';
		include $template;
	}
	return ob_get_clean();
}

function porto_load_product_attribute_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();
	$order_by_values    = porto_vc_woo_order_by();
	$order_way_values   = porto_vc_woo_order_way();

	$attributes = array();
	if ( class_exists( 'Woocommerce' ) ) {
		$attributes_tax = wc_get_attribute_taxonomies();
		foreach ( $attributes_tax as $attribute ) {
			$attributes[ $attribute->attribute_label ] = $attribute->attribute_name;
		}
	}

	// woocommerce product attribute
	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Product Attribute', 'js_composer' ),
			'base'        => 'porto_product_attribute',
			'icon'        => 'fas fa-cart-arrow-down',
			'category'    => __( 'WooCommerce', 'js_composer' ),
			'description' => __( 'Show products with an attribute shortcode', 'porto-functionality' ),
			'params'      => array_merge(
				array(
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Title', 'woocommerce' ),
						'param_name'  => 'title',
						'admin_label' => true,
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Title Border Style', 'porto-functionality' ),
						'param_name' => 'title_border_style',
						'dependency' => array(
							'element'   => 'title',
							'not_empty' => true,
						),
						'std'        => '',
						'value'      => array(
							__( 'No Border', 'porto-functionality' )     => '',
							__( 'Bottom Border', 'porto-functionality' ) => 'border-bottom',
							__( 'Middle Border', 'porto-functionality' ) => 'border-middle',
						),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Title Align', 'porto-functionality' ),
						'param_name' => 'title_align',
						'value'      => porto_sh_commons( 'align' ),
						'dependency' => array(
							'element'   => 'title',
							'not_empty' => true,
						),
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'View mode', 'porto-functionality' ),
						'param_name'  => 'view',
						'value'       => porto_sh_commons( 'products_view_mode' ),
						'admin_label' => true,
					),
					array(
						'type'       => 'porto_image_select',
						'heading'    => __( 'Grid Layout', 'porto-functionality' ),
						'param_name' => 'grid_layout',
						'dependency' => array(
							'element' => 'view',
							'value'   => array( 'creative' ),
						),
						'std'        => '1',
						'value'      => porto_sh_commons( 'masonry_layouts' ),
					),
					array(
						'type'       => 'number',
						'heading'    => __( 'Grid Height (px)', 'porto-functionality' ),
						'param_name' => 'grid_height',
						'dependency' => array(
							'element' => 'view',
							'value'   => array( 'creative' ),
						),
						'suffix'     => 'px',
						'std'        => 600,
					),
					array(
						'type'        => 'number',
						'heading'     => __( 'Column Spacing (px)', 'porto-functionality' ),
						'description' => __( 'Leave blank if you use theme default value.', 'porto-functionality' ),
						'param_name'  => 'spacing',
						'dependency'  => array(
							'element' => 'view',
							'value'   => array( 'grid', 'creative', 'products-slider' ),
						),
						'suffix'      => 'px',
						'std'         => '',
					),
					array(
						'type'        => 'number',
						'heading'     => __( 'Per page', 'js_composer' ),
						'value'       => 12,
						'param_name'  => 'per_page',
						'description' => __( 'The "per_page" shortcode determines how many products to show on the page', 'js_composer' ),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Columns', 'porto-functionality' ),
						'param_name' => 'columns',
						'dependency' => array(
							'element' => 'view',
							'value'   => array( 'products-slider', 'grid', 'divider' ),
						),
						'std'        => '4',
						'value'      => porto_sh_commons( 'products_columns' ),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Columns on mobile ( <= 575px )', 'porto-functionality' ),
						'param_name' => 'columns_mobile',
						'dependency' => array(
							'element' => 'view',
							'value'   => array( 'products-slider', 'grid', 'divider' ),
						),
						'std'        => '',
						'value'      => array(
							__( 'Default', 'porto-functionality' ) => '',
							'1' => '1',
							'2' => '2',
							'3' => '3',
						),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Column Width', 'porto-functionality' ),
						'param_name' => 'column_width',
						'dependency' => array(
							'element' => 'view',
							'value'   => array( 'products-slider', 'grid', 'divider' ),
						),
						'value'      => porto_sh_commons( 'products_column_width' ),
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Order by', 'js_composer' ),
						'param_name'  => 'orderby',
						'value'       => $order_by_values,
						/* translators: %s: Wordpress codex page */
						'description' => sprintf( __( 'Select how to sort retrieved products. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Order way', 'js_composer' ),
						'param_name'  => 'order',
						'value'       => $order_way_values,
						/* translators: %s: Wordpress codex page */
						'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Attribute', 'js_composer' ),
						'param_name'  => 'attribute',
						'value'       => $attributes,
						'save_always' => true,
						'description' => __( 'List of product taxonomy attribute', 'js_composer' ),
					),
					array(
						'type'        => 'checkbox',
						'heading'     => __( 'Filter', 'js_composer' ),
						'param_name'  => 'filter',
						'value'       => array( 'empty' => 'empty' ),
						'description' => __( 'Taxonomy values', 'js_composer' ),
						'save_always' => true,
						'dependency'  => array(
							'callback' => 'vcWoocommerceProductAttributeFilterDependencyCallback',
						),
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Product Layout', 'porto-functionality' ),
						'description' => __( 'Select position of add to cart, add to wishlist, quickview.', 'porto-functionality' ),
						'param_name'  => 'addlinks_pos',
						'value'       => porto_sh_commons( 'products_addlinks_pos' ),
					),
					array(
						'type'       => 'number',
						'heading'    => __( 'Overlay Background Opacity (%)', 'porto-functionality' ),
						'param_name' => 'overlay_bg_opacity',
						'dependency' => array(
							'element' => 'addlinks_pos',
							'value'   => array( 'onimage2', 'onimage3' ),
						),
						'suffix'     => '%',
						'std'        => '30',
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Image Size', 'porto-functionality' ),
						'param_name' => 'image_size',
						'dependency' => array(
							'element' => 'view',
							'value'   => array( 'products-slider', 'grid', 'divider', 'list' ),
						),
						'value'      => porto_sh_commons( 'image_sizes' ),
						'std'        => '',
					),
					$custom_class,
				),
				porto_vc_product_slider_fields(),
				array(
					$animation_type,
					$animation_duration,
					$animation_delay,
				)
			),
		)
	);

	//For param: "filter" param value
	//vc_form_fields_render_field_{shortcode_name}_{param_name}_param
	add_filter( 'vc_form_fields_render_field_porto_product_attribute_filter_param', 'porto_shortcode_product_attribute_filter_param', 10, 4 ); // Defines default value for param if not provided. Takes from other param value.

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Product_Attribute' ) ) {
		class WPBakeryShortCode_Porto_Product_Attribute extends WPBakeryShortCode {
		}
	}
}

function porto_shortcode_product_attribute_filter_param( $param_settings, $current_value, $map_settings, $atts ) {
	if ( class_exists( 'Vc_Vendor_Woocommerce' ) ) {
		$vc_vendor_wc = new Vc_Vendor_Woocommerce();
		return $vc_vendor_wc->productAttributeFilterParamValue( $param_settings, $current_value, $map_settings, $atts );
	}
	return '';
}
