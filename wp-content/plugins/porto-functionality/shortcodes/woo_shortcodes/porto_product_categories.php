<?php

// Porto Product Categories
add_action( 'vc_after_init', 'porto_load_product_categories_shortcode' );

function porto_load_product_categories_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();
	$order_way_values   = porto_vc_woo_order_way();

	// woocommerce product categories
	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Product Categories', 'js_composer' ),
			'base'        => 'porto_product_categories',
			'icon'        => 'fas fa-shopping-basket',
			'category'    => __( 'WooCommerce', 'js_composer' ),
			'description' => __( 'Display product categories loop', 'porto-functionality' ),
			'params'      => array_merge(
				array(
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Title', 'woocommerce' ),
						'param_name'  => 'title',
						'admin_label' => true,
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'View mode', 'porto-functionality' ),
						'param_name'  => 'view',
						'value'       => array(
							__( 'Grid', 'porto-functionality' )            => 'grid',
							__( 'Slider', 'porto-functionality' )          => 'products-slider',
							__( 'Grid - Creative', 'porto-functionality' ) => 'creative',
						),
						'admin_label' => true,
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Number', 'js_composer' ),
						'param_name'  => 'number',
						'description' => __( 'The `number` field is used to display the number of products.', 'js_composer' ),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Columns', 'porto-functionality' ),
						'param_name' => 'columns',
						'dependency' => array(
							'element' => 'view',
							'value'   => array( 'products-slider', 'grid' ),
						),
						'std'        => '4',
						'value'      => array(
							'1' => 1,
							'2' => 2,
							'3' => 3,
							'4' => 4,
							'5' => 5,
							'6' => 6,
							'7' => 7,
							'8' => 8,
							'9' => 9,
						),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Columns on mobile ( <= 575px )', 'porto-functionality' ),
						'param_name' => 'columns_mobile',
						'dependency' => array(
							'element' => 'view',
							'value'   => array( 'products-slider', 'grid' ),
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
							'value'   => array( 'products-slider', 'grid' ),
						),
						'value'      => porto_sh_commons( 'products_column_width' ),
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
						'type'       => 'textfield',
						'heading'    => __( 'Grid Height (px)', 'porto-functionality' ),
						'param_name' => 'grid_height',
						'dependency' => array(
							'element' => 'view',
							'value'   => array( 'creative' ),
						),
						'std'        => '600',
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Column Spacing (px)', 'porto-functionality' ),
						'param_name' => 'spacing',
						'dependency' => array(
							'element' => 'view',
							'value'   => array( 'creative' ),
						),
						'std'        => '',
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Text Position', 'porto-functionality' ),
						'param_name'  => 'text_position',
						'value'       => array(
							__( 'Inner Middle Left', 'porto-functionality' ) => 'middle-left',
							__( 'Inner Middle Center', 'porto-functionality' ) => 'middle-center',
							__( 'Inner Middle Right', 'porto-functionality' ) => 'middle-right',
							__( 'Inner Bottom Left', 'porto-functionality' ) => 'bottom-left',
							__( 'Inner Bottom Center', 'porto-functionality' ) => 'bottom-center',
							__( 'Inner Bottom Right', 'porto-functionality' ) => 'bottom-right',
							__( 'Outside', 'porto-functionality' )           => 'outside-center',
						),
						'qa_selector' => 'li.product-category:first-child .thumb-info-title',
						'std'         => 'middle-center',
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Overlay Background Opacity (%)', 'porto-functionality' ),
						'param_name' => 'overlay_bg_opacity',
						'std'        => '15',
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Text Color', 'porto-functionality' ),
						'param_name' => 'text_color',
						'value'      => array(
							__( 'Dark', 'porto-functionality' )  => 'dark',
							__( 'Light', 'porto-functionality' ) => 'light',
							__( 'Primary', 'porto-functionality' ) => 'primary',
							__( 'Secondary', 'porto-functionality' ) => 'secondary',
						),
						'std'        => 'light',
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Order by', 'js_composer' ),
						'param_name'  => 'orderby',
						'value'       => array(
							__( 'Title', 'porto-functionality' )         => 'name',
							__( 'ID', 'porto-functionality' )            => 'term_id',
							__( 'Product Count', 'porto-functionality' ) => 'count',
							__( 'None', 'porto-functionality' )          => 'none',
							__( 'Parent', 'porto-functionality' )        => 'parent',
							__( 'Description', 'porto-functionality' )   => 'description',
							__( 'Term Group', 'porto-functionality' )    => 'term_group',
						),
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
						'type'       => 'checkbox',
						'heading'    => __( 'Hide empty', 'js_composer' ),
						'param_name' => 'hide_empty',
						'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Parent Category ID', 'porto-functionality' ),
						'param_name' => 'parent',
					),
					array(
						'type'        => 'autocomplete',
						'heading'     => __( 'Categories', 'js_composer' ),
						'param_name'  => 'ids',
						'settings'    => array(
							'multiple' => true,
							'sortable' => true,
						),
						'description' => __( 'List of product categories', 'js_composer' ),
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Media Type', 'porto-functionality' ),
						'param_name'  => 'media_type',
						'description' => __( 'If you want to use icon type, you need to input category icon in categoriy edit page.', 'porto-functionality' ),
						'value'       => array(
							__( 'Image', 'porto-functionality' ) => '',
							__( 'Icon', 'porto-functionality' )  => 'icon',
							__( 'None', 'porto-functionality' )  => 'none',
						),
						'std'         => '',
						'group'       => __( 'Category Option', 'porto-functionality' ),
					),
					array(
						'type'       => 'checkbox',
						'heading'    => __( 'Display sub categories', 'porto-functionality' ),
						'param_name' => 'show_sub_cats',
						'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
						'group'      => __( 'Category Option', 'porto-functionality' ),
					),
					array(
						'type'        => 'checkbox',
						'heading'     => __( 'Display a featured product', 'porto-functionality' ),
						'description' => __( 'If you check this option, a featured product in each category will be displayed under the product category. This option isn\'t available to "Grid-Creative" of "View".', 'porto-functionality' ),
						'param_name'  => 'show_featured',
						'value'       => array( __( 'Yes', 'js_composer' ) => 'yes' ),
						'group'       => __( 'Category Option', 'porto-functionality' ),
					),
					array(
						'type'       => 'checkbox',
						'heading'    => __( 'Hide products count', 'porto-functionality' ),
						'param_name' => 'hide_count',
						'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
						'group'      => __( 'Category Option', 'porto-functionality' ),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Hover Effect', 'porto-functionality' ),
						'param_name' => 'hover_effect',
						'value'      => array(
							__( 'Normal', 'porto-functionality' ) => '',
							__( 'Display product count on hover', 'porto-functionality' ) => 'show-count-on-hover',
						),
						'std'        => '',
						'group'      => __( 'Category Option', 'porto-functionality' ),
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
						'group'      => __( 'Category Option', 'porto-functionality' ),
					),
					array(
						'type'        => 'porto_number',
						'heading'     => __( 'Image Border Radius', 'porto-functionality' ),
						'description' => __( 'To control the border radius of image', 'porto-functionality' ),
						'param_name'  => 'thumb_border_radius',
						'units'       => array( 'px', '%' ),
						'dependency'  => array(
							'element' => 'media_type',
							'value'   => array( '' ),
						),
						'selectors'   => array(
							'{{WRAPPER}} .thumb-info .thumb-info-wrapper' => 'border-radius: {{VALUE}}{{UNIT}};',
						),
						'qa_selector' => 'li.product-category:first-child',
						'group'       => __( 'Style', 'porto-functionality' ),
					),
					array(
						'type'        => 'porto_number',
						'heading'     => __( 'Min Height of Text', 'porto-functionality' ),
						'description' => __( 'To control the min height of category', 'porto-functionality' ),
						'param_name'  => 'thumb_min_height',
						'units'       => array( 'px' ),
						'dependency'  => array(
							'element' => 'media_type',
							'value'   => array( 'none' ),
						),
						'selectors'   => array(
							'{{WRAPPER}} .product-category .thumb-info' => 'min-height: {{VALUE}}{{UNIT}};',
						),
						'group'       => __( 'Style', 'porto-functionality' ),
					),
					array(
						'type'        => 'porto_dimension',
						'heading'     => __( 'Icon Padding (px)', 'porto-functionality' ),
						'description' => __( 'To control the icon padding', 'porto-functionality' ),
						'param_name'  => 'icon_padding',
						'value'       => '',
						'dependency'  => array(
							'element' => 'media_type',
							'value'   => array( 'icon' ),
						),
						'selectors'   => array(
							'{{WRAPPER}} .thumb-info i' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
						),
						'group'       => __( 'Style', 'porto-functionality' ),
					),
					array(
						'type'        => 'porto_number',
						'heading'     => __( 'Icon Border Radius', 'porto-functionality' ),
						'description' => __( 'To control the border radius of icon background.', 'porto-functionality' ),
						'param_name'  => 'icon_border_radius',
						'units'       => array( 'px', '%' ),
						'dependency'  => array(
							'element' => 'media_type',
							'value'   => array( 'icon' ),
						),
						'selectors'   => array(
							'{{WRAPPER}} .cat-has-icon .thumb-info i' => 'border-radius: {{VALUE}}{{UNIT}};',
						),
						'group'       => __( 'Style', 'porto-functionality' ),
					),
					array(
						'type'       => 'porto_param_heading',
						'param_name' => 'bg_color',
						'text'       => __( 'Background color', 'porto-functionality' ),
						'group'      => __( 'Style', 'porto-functionality' ),
						'dependency' => array(
							'element' => 'media_type',
							'value'   => array( 'none', 'icon' ),
						),
					),
					array(
						'type'        => 'colorpicker',
						'heading'     => __( 'Background Color', 'porto-functionality' ),
						'description' => __( 'To set category background color', 'porto-functionality' ),
						'param_name'  => 'thumb_bg_color',
						'dependency'  => array(
							'element' => 'media_type',
							'value'   => array( 'none', 'icon' ),
						),
						'selectors'   => array(
							'{{WRAPPER}} .thumb-info' => 'background-color: {{VALUE}};',
						),
						'group'       => __( 'Style', 'porto-functionality' ),
					),
					array(
						'type'        => 'colorpicker',
						'heading'     => __( 'Background Hover Color', 'porto-functionality' ),
						'description' => __( 'To set category background hover color', 'porto-functionality' ),
						'param_name'  => 'thumb_bg_color_hover',
						'dependency'  => array(
							'element' => 'media_type',
							'value'   => array( 'none', 'icon' ),
						),
						'selectors'   => array(
							'{{WRAPPER}} .thumb-info:hover' => 'background-color: {{VALUE}};',
						),
						'group'       => __( 'Style', 'porto-functionality' ),
					),
					array(
						'type'        => 'colorpicker',
						'heading'     => __( 'Icon Background Color', 'porto-functionality' ),
						'description' => __( 'To set icon background color', 'porto-functionality' ),
						'param_name'  => 'icon_bg_color',
						'dependency'  => array(
							'element' => 'media_type',
							'value'   => array( 'icon' ),
						),
						'selectors'   => array(
							'{{WRAPPER}} .cat-has-icon .thumb-info i' => 'background-color: {{VALUE}};',
						),
						'group'       => __( 'Style', 'porto-functionality' ),
					),
					array(
						'type'        => 'colorpicker',
						'heading'     => __( 'Icon Background Hover Color', 'porto-functionality' ),
						'description' => __( 'To set icon background hover color', 'porto-functionality' ),
						'param_name'  => 'icon_bg_color_hover',
						'dependency'  => array(
							'element' => 'media_type',
							'value'   => array( 'icon' ),
						),
						'selectors'   => array(
							'{{WRAPPER}} .cat-has-icon .thumb-info:hover i' => 'background-color: {{VALUE}};',
						),
						'group'       => __( 'Style', 'porto-functionality' ),
					),
					array(
						'type'        => 'porto_typography',
						'heading'     => __( 'Title Typography', 'porto-functionality' ),
						'param_name'  => 'title_typo',
						'description' => __( 'To control the title typography', 'porto-functionality' ),
						'selectors'   => array(
							'{{WRAPPER}} .product-category .thumb-info h3',
						),
						'qa_selector' => 'li.product-category:first-child .thumb-info h3',
						'group'       => __( 'Text Style', 'porto-functionality' ),
					),
					array(
						'type'        => 'porto_typography',
						'heading'     => __( 'Category Typography', 'porto-functionality' ),
						'param_name'  => 'category_typo',
						'description' => __( 'To control the category typography', 'porto-functionality' ),
						'dependency'  => array(
							'element'   => 'show_sub_cats',
							'not_empty' => true,
						),
						'selectors'   => array(
							'{{WRAPPER}} .sub-categories li',
						),
						'group'       => __( 'Text Style', 'porto-functionality' ),
					),
					array(
						'type'        => 'porto_typography',
						'heading'     => __( 'Product Count Typography', 'porto-functionality' ),
						'description' => __( 'To control the count typography', 'porto-functionality' ),
						'param_name'  => 'product_count',
						'dependency'  => array(
							'element'  => 'hide_count',
							'is_empty' => true,
						),
						'selectors'   => array(
							'{{WRAPPER}} .product-category .thumb-info-type',
						),
						'group'       => __( 'Text Style', 'porto-functionality' ),
					),
					array(
						'type'        => 'porto_typography',
						'heading'     => __( 'Icon Typography', 'porto-functionality' ),
						'description' => __( 'To control the icon typography', 'porto-functionality' ),
						'param_name'  => 'icon_typo',
						'dependency'  => array(
							'element' => 'media_type',
							'value'   => array( 'icon' ),
						),
						'selectors'   => array(
							'{{WRAPPER}} .thumb-info i',
						),
						'group'       => __( 'Text Style', 'porto-functionality' ),
					),
					array(
						'type'        => 'colorpicker',
						'heading'     => __( 'Color', 'porto-functionality' ),
						'param_name'  => 'text_normal_color',
						'description' => __( 'To control the color of category', 'porto-functionality' ),
						'selectors'   => array(
							'{{WRAPPER}} .thumb-info-title' => 'color: {{VALUE}} !important;',
							'{{WRAPPER}} .thumb-info i' => 'color: {{VALUE}} !important;',
						),
						'group'       => __( 'Text Style', 'porto-functionality' ),
					),
					array(
						'type'        => 'colorpicker',
						'heading'     => __( 'Hover Color', 'porto-functionality' ),
						'description' => __( 'To control the hover color of category', 'porto-functionality' ),
						'param_name'  => 'text_hover_color',
						'selectors'   => array(
							'{{WRAPPER}} .thumb-info:hover .thumb-info-title' => 'color: {{VALUE}} !important;',
							'{{WRAPPER}} .thumb-info:hover i' => 'color: {{VALUE}} !important;',
							'{{WRAPPER}} .thumb-info .thumb-info-inner' => 'transition: none;',
						),
						'group'       => __( 'Text Style', 'porto-functionality' ),
					),
					array(
						'type'       => 'porto_dimension',
						'heading'    => __( 'Text Padding (px)', 'porto-functionality' ),
						'param_name' => 'thumb_padding',
						'value'      => '',
						'dependency' => array(
							'element' => 'media_type',
							'value'   => array( 'icon', 'none' ),
						),
						'selectors'  => array(
							'{{WRAPPER}} .product-category:not(.cat-has-icon) .thumb-info .thumb-info-title' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
							'{{WRAPPER}} .product-category.cat-has-icon .thumb-info' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
						),
						'group'      => __( 'Text Style', 'porto-functionality' ),
					),
					$custom_class,
				),
				porto_vc_product_slider_fields( 'products-slider', 'dots-style-1' ),
				array(
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Stage Padding', 'porto-functionality' ),
						'param_name'  => 'stage_padding',
						'value'       => '',
						'description' => 'unit: px',
						'dependency'  => array(
							'element' => 'view',
							'value'   => 'products-slider',
						),
						'group'       => __( 'Slider Options', 'porto-functionality' ),
					),
					$animation_type,
					$animation_duration,
					$animation_delay,
				)
			),
		)
	);

	//Filters For autocomplete param:
	//For suggestion: vc_autocomplete_[shortcode_name]_[param_name]_callback
	add_filter( 'vc_autocomplete_porto_product_categories_ids_callback', 'porto_shortcode_product_categories_ids_callback', 10, 1 ); // Get suggestion(find). Must return an array
	add_filter( 'vc_autocomplete_porto_product_categories_ids_render', 'porto_shortcode_product_categories_ids_render', 10, 1 ); // Render exact category by id. Must return an array (label,value)

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Product_Categories' ) ) {
		class WPBakeryShortCode_Porto_Product_Categories extends WPBakeryShortCode {
		}
	}
}

function porto_shortcode_product_categories_ids_callback( $query ) {
	if ( class_exists( 'Vc_Vendor_Woocommerce' ) ) {
		$vc_vendor_wc = new Vc_Vendor_Woocommerce();
		return $vc_vendor_wc->productCategoryCategoryAutocompleteSuggester( $query );
	}
	return '';
}

function porto_shortcode_product_categories_ids_render( $query ) {
	if ( class_exists( 'Vc_Vendor_Woocommerce' ) ) {
		$vc_vendor_wc = new Vc_Vendor_Woocommerce();
		return $vc_vendor_wc->productCategoryCategoryRenderByIdExact( $query );
	}
	return '';
}
