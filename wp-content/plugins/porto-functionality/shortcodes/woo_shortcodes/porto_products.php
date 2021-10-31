<?php

// Porto Products
if ( function_exists( 'register_block_type' ) ) {
	register_block_type(
		'porto/porto-products',
		array(
			'editor_script'   => 'porto_blocks',
			'render_callback' => 'porto_shortcode_products',
		)
	);
}
add_shortcode( 'porto_products', 'porto_shortcode_products' );
add_action( 'vc_after_init', 'porto_load_products_shortcode' );

function porto_shortcode_products( $atts, $content = null ) {
	ob_start();
	if ( $template = porto_shortcode_woo_template( 'porto_products' ) ) {
		include $template;
	}
	return ob_get_clean();
}

function porto_load_products_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();
	$order_way_values   = porto_vc_woo_order_way();

	$status_values = array(
		__( 'All', 'porto-functionality' )             => '',
		__( 'Recently Viewed', 'porto-functionality' ) => 'viewed',
	);
	global $porto_settings;
	if ( ! empty( $porto_settings['woo-pre-order'] ) ) {
		$status_values[ __( 'Pre-Order', 'porto-functionality' ) ] = 'pre_order';
	}

	// woocommerce products
	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Products', 'js_composer' ),
			'base'        => 'porto_products',
			'icon'        => 'fas fa-cart-arrow-down',
			'category'    => __( 'WooCommerce', 'js_composer' ),
			'description' => __( 'Show multiple products by ID or SKU.', 'js_composer' ),
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
				),
				array(
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Status', 'porto-functionality' ),
						'param_name'  => 'status',
						'value'       => $status_values,
						'admin_label' => true,
					),
				),
				array(
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
							'value'   => array( 'products-slider', 'grid', 'divider', 'list' ),
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
						'type'       => 'dropdown',
						'heading'    => __( 'Pagination Style', 'porto-functionality' ),
						'param_name' => 'pagination_style',
						'dependency' => array(
							'element' => 'view',
							'value'   => array( 'list', 'grid', 'divider' ),
						),
						'std'        => '',
						'value'      => array(
							__( 'No pagination', 'porto-functionality' ) => '',
							__( 'Default' )   => 'default',
							__( 'Load more' ) => 'load_more',
						),
					),
					array(
						'type'        => 'number',
						'heading'     => __( 'Number of Products per page', 'porto-functionality' ),
						'description' => __( 'Leave blank if you use default value.', 'porto-functionality' ),
						'param_name'  => 'count',
						'admin_label' => true,
					),
					array(
						'type'       => 'porto_multiselect',
						'heading'    => __( 'Show Sort by', 'porto-functionality' ),
						'param_name' => 'show_sort',
						'std'        => '',
						'value'      => porto_woo_sort_by(),
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Title for "Sort by Popular"', 'porto-functionality' ),
						'param_name' => 'show_sales_title',
						'dependency' => array(
							'element' => 'show_sort',
							'value'   => 'popular',
						),
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Title for "Sort by Date"', 'porto-functionality' ),
						'param_name' => 'show_new_title',
						'dependency' => array(
							'element' => 'show_sort',
							'value'   => 'date',
						),
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Title for "Sort by Rating"', 'porto-functionality' ),
						'param_name' => 'show_rating_title',
						'dependency' => array(
							'element' => 'show_sort',
							'value'   => 'rating',
						),
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Title for "On Sale"', 'porto-functionality' ),
						'param_name' => 'show_onsale_title',
						'dependency' => array(
							'element' => 'show_sort',
							'value'   => 'onsale',
						),
					),
					array(
						'type'        => 'checkbox',
						'heading'     => __( 'Show category filter', 'porto-functionality' ),
						'param_name'  => 'category_filter',
						'std'         => '',
						'admin_label' => true,
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Filter Style', 'porto-functionality' ),
						'param_name'  => 'filter_style',
						'value'       => array(
							__( 'Vertical', 'porto-functionality' )   => '',
							__( 'Horizontal', 'porto-functionality' ) => 'horizontal',
						),
						'description' => __( 'This field is used only when using "sort by" or "category filter".', 'porto-functionality' ),
					),
					array(
						'type'        => 'autocomplete',
						'heading'     => __( 'Order by', 'js_composer' ),
						'param_name'  => 'orderby',
						'settings'    => array(
							'multiple' => true,
							'sortable' => true,
							'groups'   => true,
						),                      /* translators: %s: Wordpress codex page */
						'description' => sprintf( __( 'Values: id, date, menu order, title, random, raing, popularity and so on. Select how to sort retrieved products. More at %s.', 'porto-functionality' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
					),
					array(
						'type'        => 'porto_button_group',
						'heading'     => __( 'Order way for Date', 'porto-functionality' ),
						'param_name'  => 'order_date',
						/* translators: %s: Wordpress codex page */
						'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						'value'       => array(
							'DESC' => array(
								'title' => esc_html__( 'Descending', 'porto-functionality' ),
							),
							'ASC'  => array(
								'title' => esc_html__( 'Ascending', 'porto-functionality' ),
							),
						),
						'dependency'  => array(
							'element' => 'orderby',
							'value'   => 'date',
						),
						'std'         => 'DESC',
					),
					array(
						'type'        => 'porto_button_group',
						'heading'     => __( 'Order way for ID', 'porto-functionality' ),
						'param_name'  => 'order_id',
						/* translators: %s: Wordpress codex page */
						'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						'value'       => array(
							'DESC' => array(
								'title' => esc_html__( 'Descending', 'porto-functionality' ),
							),
							'ASC'  => array(
								'title' => esc_html__( 'Ascending', 'porto-functionality' ),
							),
						),
						'dependency'  => array(
							'element' => 'orderby',
							'value'   => 'id',
						),
						'std'         => 'DESC',
					),
					array(
						'type'        => 'porto_button_group',
						'heading'     => __( 'Order way for Title', 'porto-functionality' ),
						'param_name'  => 'order_title',
						/* translators: %s: Wordpress codex page */
						'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						'value'       => array(
							'DESC' => array(
								'title' => esc_html__( 'Descending', 'porto-functionality' ),
							),
							'ASC'  => array(
								'title' => esc_html__( 'Ascending', 'porto-functionality' ),
							),
						),
						'dependency'  => array(
							'element' => 'orderby',
							'value'   => 'title',
						),
						'std'         => 'DESC',
					),
					array(
						'type'        => 'porto_button_group',
						'heading'     => __( 'Order way for Random', 'porto-functionality' ),
						'param_name'  => 'order_rand',
						/* translators: %s: Wordpress codex page */
						'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						'value'       => array(
							'DESC' => array(
								'title' => esc_html__( 'Descending', 'porto-functionality' ),
							),
							'ASC'  => array(
								'title' => esc_html__( 'Ascending', 'porto-functionality' ),
							),
						),
						'dependency'  => array(
							'element' => 'orderby',
							'value'   => 'rand',
						),
						'std'         => 'DESC',
					),
					array(
						'type'        => 'porto_button_group',
						'heading'     => __( 'Order way for Menu Order', 'porto-functionality' ),
						'param_name'  => 'order_menu_order',
						/* translators: %s: Wordpress codex page */
						'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						'value'       => array(
							'DESC' => array(
								'title' => esc_html__( 'Descending', 'porto-functionality' ),
							),
							'ASC'  => array(
								'title' => esc_html__( 'Ascending', 'porto-functionality' ),
							),
						),
						'dependency'  => array(
							'element' => 'orderby',
							'value'   => 'menu_order',
						),
						'std'         => 'DESC',
					),
					array(
						'type'        => 'porto_button_group',
						'heading'     => __( 'Order way for Price', 'porto-functionality' ),
						'param_name'  => 'order_price',
						/* translators: %s: Wordpress codex page */
						'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						'value'       => array(
							'DESC' => array(
								'title' => esc_html__( 'Descending', 'porto-functionality' ),
							),
							'ASC'  => array(
								'title' => esc_html__( 'Ascending', 'porto-functionality' ),
							),
						),
						'dependency'  => array(
							'element' => 'orderby',
							'value'   => 'price',
						),
						'std'         => 'DESC',
					),
					array(
						'type'        => 'porto_button_group',
						'heading'     => __( 'Order way for Popularity', 'porto-functionality' ),
						'param_name'  => 'order_popularity',
						/* translators: %s: Wordpress codex page */
						'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						'value'       => array(
							'DESC' => array(
								'title' => esc_html__( 'Descending', 'porto-functionality' ),
							),
							'ASC'  => array(
								'title' => esc_html__( 'Ascending', 'porto-functionality' ),
							),
						),
						'dependency'  => array(
							'element' => 'orderby',
							'value'   => 'popularity',
						),
						'std'         => 'DESC',
					),
					array(
						'type'        => 'porto_button_group',
						'heading'     => __( 'Order way for Rating', 'porto-functionality' ),
						'param_name'  => 'order_rating',
						/* translators: %s: Wordpress codex page */
						'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						'value'       => array(
							'DESC' => array(
								'title' => esc_html__( 'Descending', 'porto-functionality' ),
							),
							'ASC'  => array(
								'title' => esc_html__( 'Ascending', 'porto-functionality' ),
							),
						),
						'dependency'  => array(
							'element' => 'orderby',
							'value'   => 'rating',
						),
						'std'         => 'DESC',
					),
					array(
						'type'        => 'autocomplete',
						'heading'     => __( 'Products', 'js_composer' ),
						'param_name'  => 'ids',
						'settings'    => array(
							'multiple'      => true,
							'sortable'      => true,
							'unique_values' => true,
							// In UI show results except selected. NB! You should manually check values in backend
						),
						'description' => __( 'Enter List of Products', 'js_composer' ),
						'admin_label' => true,
					),
					array(
						'type'       => 'hidden',
						'param_name' => 'skus',
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Product Layout', 'porto-functionality' ),
						'description' => __( 'Select position of add to cart, add to wishlist, quickview.', 'porto-functionality' ),
						'param_name'  => 'addlinks_pos',
						'value'       => porto_sh_commons( 'products_addlinks_pos' ),
					),
					array(
						'type'        => 'checkbox',
						'heading'     => __( 'Use simple layout?', 'porto-functionality' ),
						'description' => __( 'If you check this option, it will display product title and price only.', 'porto-functionality' ),
						'param_name'  => 'use_simple',
						'std'         => 'no',
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

	//Filters For autocomplete param:
	//For suggestion: vc_autocomplete_[shortcode_name]_[param_name]_callback
	add_filter( 'vc_autocomplete_porto_products_ids_callback', 'porto_shortcode_products_ids_callback', 10, 1 ); // Get suggestion(find). Must return an array
	add_filter( 'vc_autocomplete_porto_products_ids_render', 'porto_shortcode_products_ids_render', 10, 1 ); // Render exact product. Must return an array (label,value)
	//For param: ID default value filter
	add_filter( 'vc_form_fields_render_field_porto_products_ids_param_value', 'porto_shortcode_products_ids_param_value', 10, 4 ); // Defines default value for param if not provided. Takes from other param value.

	// add_filter( 'vc_autocomplete_porto_products_orderby_callback',  );
	add_filter(
		'vc_autocomplete_porto_products_orderby_render',
		function( $query ) {
			$options         = array();
			$order_by_values = porto_vc_woo_order_by();
			if ( count( $order_by_values ) ) {
				foreach ( $order_by_values as $key => $value ) {
					if ( ! empty( $value ) && false !== strpos( $value, $query['value'] ) ) {
						$options = array(
							'label' => $key,
							'value' => $value,
						);
						break;
					}
				}
			}
			return $options;
		}
	);
	add_filter(
		'vc_autocomplete_porto_products_orderby_callback',
		function( $query ) {
			$options         = array();
			$order_by_values = porto_vc_woo_order_by();
			if ( count( $order_by_values ) ) {
				foreach ( $order_by_values as $key => $value ) {
					if ( false !== strpos( $value, $query ) ) {
						$options[] = array(
							'label' => $key,
							'value' => $value,
						);
					}
				}
			}
			return $options;
		}
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Products' ) ) {
		class WPBakeryShortCode_Porto_Products extends WPBakeryShortCode {
		}
	}
}

function porto_shortcode_products_ids_callback( $query ) {
	if ( class_exists( 'Vc_Vendor_Woocommerce' ) ) {
		$vc_vendor_wc = new Vc_Vendor_Woocommerce();
		return $vc_vendor_wc->productIdAutocompleteSuggester( $query );
	}
	return '';
}

function porto_shortcode_products_ids_render( $query ) {
	if ( class_exists( 'Vc_Vendor_Woocommerce' ) ) {
		$vc_vendor_wc = new Vc_Vendor_Woocommerce();
		return $vc_vendor_wc->productIdAutocompleteRender( $query );
	}
	return '';
}

function porto_shortcode_products_ids_param_value( $current_value, $param_settings, $map_settings, $atts ) {
	if ( class_exists( 'Vc_Vendor_Woocommerce' ) ) {
		$vc_vendor_wc = new Vc_Vendor_Woocommerce();
		return $vc_vendor_wc->productsIdsDefaultValue( $current_value, $param_settings, $map_settings, $atts );
	}
	return '';
}
