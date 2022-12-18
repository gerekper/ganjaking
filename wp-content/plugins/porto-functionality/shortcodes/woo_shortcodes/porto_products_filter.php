<?php

// Porto Widget Woo Products
add_action( 'vc_after_init', 'porto_load_shortcode_products_filter' );

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
					'type'       => 'porto_param_heading',
					'param_name' => 'description_price',
					'text'       => esc_html__( 'Price', 'porto-functionality' ),
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
					'type'       => 'porto_param_heading',
					'param_name' => 'description_option',
					'text'       => esc_html__( 'Option', 'porto-functionality' ),
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
					'type'       => 'porto_param_heading',
					'param_name' => 'description_submit',
					'text'       => esc_html__( 'Submit Button', 'porto-functionality' ),
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
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'description_extra',
					'text'       => esc_html__( 'Extra Option', 'porto-functionality' ),
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Set Inline', 'porto-functionality' ),
					'description' => __( 'If you set this option, you can control the width of selectors and button.', 'porto-functionality' ),
					'param_name'  => 'set_inline',
					'dependency'  => array(
						'element' => 'display_type',
						'value'   => array( '' ),
					),
					'selectors'   => array(
						'{{WRAPPER}}.porto_products_filter_form' => 'display: flex; flex-wrap: wrap;',
						'{{WRAPPER}}.porto_products_filter_form .btn-submit' => 'margin-top: 0;',
					),
					'group'       => __( 'Select Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'porto_number',
					'heading'     => __( 'Vertical Space', 'porto-functionality' ),
					'description' => __( 'To control the vertical space of filter item.', 'porto-functionality' ),
					'param_name'  => 'vertical_space',
					'units'       => array( 'px', 'rem' ),
					'dependency'  => array(
						'element'  => 'set_inline',
						'is_empty' => true,
					),
					'selectors'   => array(
						'{{WRAPPER}} select' => 'margin-bottom: {{VALUE}}{{UNIT}};',
					),
					'group'       => __( 'Select Style', 'porto-functionality' ),
				),

				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Dropdown Arrow Position', 'porto-functionality' ),
					'param_name' => 'arrow_pos',
					'units'      => array( '%' ),
					'selectors'  => array(
						'{{WRAPPER}} select' => 'background-position: {{VALUE}}{{UNIT}};',
					),
					'group'      => __( 'Select Style', 'porto-functionality' ),
				),

				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Typography', 'porto-functionality' ),
					'param_name' => 'select_typography',
					'dependency' => array(
						'element' => 'display_type',
						'value'   => array( '' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} select',
					),
					'group'      => __( 'Select Style', 'porto-functionality' ),
				),

				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Background Color', 'porto-functionality' ),
					'param_name' => 'selector_bg_color',
					'dependency' => array(
						'element' => 'display_type',
						'value'   => array( '' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} select' => 'background-color: {{VALUE}};',
					),
					'group'      => __( 'Select Style', 'porto-functionality' ),
				),

				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'param_name' => 'selector_color',
					'dependency' => array(
						'element' => 'display_type',
						'value'   => array( '' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} select' => 'color: {{VALUE}};',
					),
					'group'      => __( 'Select Style', 'porto-functionality' ),
				),

				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'select_width_height',
					'text'       => __( 'Selector Layout', 'porto-functionality' ),
					'group'      => __( 'Select Style', 'porto-functionality' ),
					'dependency' => array(
						'element' => 'display_type',
						'value'   => array( '' ),
					),
				),

				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Select Width', 'porto-functionality' ),
					'param_name' => 'select_width',
					'units'      => array( '%' ),
					'dependency' => array(
						'element' => 'display_type',
						'value'   => array( '' ),
					),
					'selectors'  => array(
						'{{WRAPPER}}' => '--porto-product-filter-select-width: {{VALUE}}{{UNIT}};',
					),
					'group'      => __( 'Select Style', 'porto-functionality' ),
				),

				array(
					'type'        => 'porto_number',
					'heading'     => __( 'Horizontal Space', 'porto-functionality' ),
					'description' => __( 'To control the horizontal space of filter item.', 'porto-functionality' ),
					'param_name'  => 'hr_space',
					'units'       => array( 'px', 'rem' ),
					'dependency'  => array(
						'element'   => 'set_inline',
						'not_empty' => true,
					),
					'selectors'   => array(
						'{{WRAPPER}}' => '--porto-product-filter-space: {{VALUE}}{{UNIT}};',
					),
					'group'       => __( 'Select Style', 'porto-functionality' ),
				),

				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Select Width (< 992px)', 'porto-functionality' ),
					'param_name' => 'select_width_md',
					'units'      => array( '%' ),
					'dependency' => array(
						'element' => 'display_type',
						'value'   => array( '' ),
					),
					'selectors'  => array(
						'{{WRAPPER}}' => '--porto-product-filter-select-width-md: {{VALUE}}{{UNIT}};',
					),
					'group'      => __( 'Select Style', 'porto-functionality' ),
				),

				array(
					'type'        => 'porto_number',
					'heading'     => __( 'Horizontal Space (< 992px)', 'porto-functionality' ),
					'description' => __( 'To control the horizontal space of filter item.', 'porto-functionality' ),
					'param_name'  => 'hr_space_md',
					'units'       => array( 'px', 'rem' ),
					'dependency'  => array(
						'element'   => 'set_inline',
						'not_empty' => true,
					),
					'selectors'   => array(
						'{{WRAPPER}}' => '--porto-product-filter-space-md: {{VALUE}}{{UNIT}};',
					),
					'group'       => __( 'Select Style', 'porto-functionality' ),
				),

				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Height', 'porto-functionality' ),
					'param_name' => 'selector_height',
					'units'      => array( 'px', 'rem' ),
					'dependency' => array(
						'element' => 'display_type',
						'value'   => array( '' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} select' => 'height: {{VALUE}}{{UNIT}};',
					),
					'group'      => __( 'Select Style', 'porto-functionality' ),
				),

				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'select_border',
					'text'       => __( 'Border', 'porto-functionality' ),
					'group'      => __( 'Select Style', 'porto-functionality' ),
					'dependency' => array(
						'element' => 'display_type',
						'value'   => array( '' ),
					),
				),

				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Border Radius', 'porto-functionality' ),
					'param_name' => 'selector_b_r',
					'units'      => array( 'px' ),
					'dependency' => array(
						'element' => 'display_type',
						'value'   => array( '' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} select' => 'border-radius: {{VALUE}}{{UNIT}};',
					),
					'group'      => __( 'Select Style', 'porto-functionality' ),
				),

				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Border Width', 'porto-functionality' ),
					'param_name' => 'selector_br_width',
					'units'      => array( 'px' ),
					'dependency' => array(
						'element' => 'display_type',
						'value'   => array( '' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} select' => 'border-width: {{VALUE}}{{UNIT}};',
					),
					'group'      => __( 'Select Style', 'porto-functionality' ),
				),

				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Border Color', 'porto-functionality' ),
					'param_name' => 'selector_br_color',
					'dependency' => array(
						'element' => 'display_type',
						'value'   => array( '' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} select' => 'border-color: {{VALUE}};',
					),
					'group'      => __( 'Select Style', 'porto-functionality' ),
				),

				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Typography', 'porto-functionality' ),
					'param_name' => 'submit_typography',
					'dependency' => array(
						'element' => 'display_type',
						'value'   => array( '' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .btn-submit',
					),
					'group'      => __( 'Submit Button Style', 'porto-functionality' ),
				),

				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'submit_width_style',
					'text'       => __( 'Submit Layout', 'porto-functionality' ),
					'group'      => __( 'Submit Button Style', 'porto-functionality' ),
					'dependency' => array(
						'element' => 'display_type',
						'value'   => array( '' ),
					),
				),

				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Button Width', 'porto-functionality' ),
					'param_name' => 'submit_width',
					'units'      => array( '%' ),
					'dependency' => array(
						'element' => 'display_type',
						'value'   => array( '' ),
					),
					'selectors'  => array(
						'{{WRAPPER}}' => '--porto-product-filter-submit-width: {{VALUE}}{{UNIT}};',
					),
					'group'      => __( 'Submit Button Style', 'porto-functionality' ),
				),

				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Select Width (< 992px)', 'porto-functionality' ),
					'param_name' => 'submit_width_md',
					'units'      => array( '%' ),
					'dependency' => array(
						'element' => 'display_type',
						'value'   => array( '' ),
					),
					'selectors'  => array(
						'{{WRAPPER}}' => '--porto-product-filter-submit-width-md: {{VALUE}}{{UNIT}};',
					),
					'group'      => __( 'Submit Button Style', 'porto-functionality' ),
				),

				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Height', 'porto-functionality' ),
					'param_name' => 'submit_height',
					'units'      => array( 'px', 'rem' ),
					'dependency' => array(
						'element' => 'display_type',
						'value'   => array( '' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .btn-submit' => 'height: {{VALUE}}{{UNIT}};',
					),
					'group'      => __( 'Submit Button Style', 'porto-functionality' ),
				),

				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'submit_border',
					'text'       => __( 'Border', 'porto-functionality' ),
					'group'      => __( 'Submit Button Style', 'porto-functionality' ),
					'dependency' => array(
						'element' => 'display_type',
						'value'   => array( '' ),
					),
				),

				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Border Radius', 'porto-functionality' ),
					'param_name' => 'submit_b_r',
					'units'      => array( 'px' ),
					'dependency' => array(
						'element' => 'display_type',
						'value'   => array( '' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .btn-submit' => 'border-radius: {{VALUE}}{{UNIT}};',
					),
					'group'      => __( 'Submit Button Style', 'porto-functionality' ),
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
