<?php

// Porto FAQs
add_action( 'vc_after_init', 'porto_load_faqs_shortcode' );

function porto_load_faqs_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => 'Porto ' . __( 'FAQs', 'porto-functionality' ),
			'base'        => 'porto_faqs',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Show faqs with accordion', 'porto-functionality' ),
			'icon'        => 'fas fa-question-circle',
			'params'      => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Title', 'porto-functionality' ),
					'param_name'  => 'title',
					'admin_label' => true,
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Category IDs', 'porto-functionality' ),
					'description' => __( 'comma separated list of category ids', 'porto-functionality' ),
					'param_name'  => 'cats',
					'admin_label' => true,
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'FAQ IDs', 'porto-functionality' ),
					'description' => __( 'comma separated list of faq ids', 'porto-functionality' ),
					'param_name'  => 'post_in',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'FAQs Count (per page)', 'porto-functionality' ),
					'param_name' => 'number',
					'value'      => '8',
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Archive Link', 'porto-functionality' ),
					'param_name' => 'view_more',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Extra class name for Archive Link', 'porto-functionality' ),
					'param_name' => 'view_more_class',
					'dependency' => array(
						'element'   => 'view_more',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Category Filter', 'porto-functionality' ),
					'param_name' => 'filter',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Filter Type', 'porto-functionality' ),
					'param_name' => 'filter_type',
					'std'        => '',
					'value'      => array(
						__( 'Filter using Javascript/CSS', 'porto-functionality' ) => '',
						__( 'Ajax Loading', 'porto-functionality' ) => 'ajax',
					),
					'dependency' => array(
						'element'   => 'filter',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Pagination Style', 'porto-functionality' ),
					'param_name' => 'pagination',
					'std'        => '',
					'value'      => array(
						__( 'None', 'porto-functionality' ) => '',
						__( 'Ajax Pagination', 'porto-functionality' ) => 'yes',
						__( 'Infinite Scroll', 'porto-functionality' ) => 'infinite',
						__( 'Load More (Button)', 'porto-functionality' ) => 'load_more',
					),
				),

				array(
					'type'        => 'porto_number',
					'heading'     => __( 'Spacing between items', 'porto-functionality' ),
					'param_name'  => 'spacing',
					'units'       => array( 'px', 'rem', 'em' ),
					'selectors'   => array(
						'{{WRAPPER}} .toggle' => 'margin-bottom: {{VALUE}}{{UNIT}}; padding-bottom: 0;',
					),
					'qa_selector' => '.faq-row>.faq:first-child .toggle',
					'group'       => __( 'Faq', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Border Radius', 'porto-functionality' ),
					'param_name' => 'faq_br',
					'value'      => '',
					'selectors'  => array(
						'{{WRAPPER}} .toggle' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'group'      => __( 'Faq', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Border Style', 'porto-functionality' ),
					'param_name' => 'faq_bs',
					'std'        => '',
					'value'      => array(
						__( 'Default', 'porto-functionality' ) => '',
						__( 'None', 'porto-functionality' )   => 'none',
						__( 'Solid', 'porto-functionality' )  => 'solid',
						__( 'Dashed', 'porto-functionality' ) => 'dashed',
						__( 'Dotted', 'porto-functionality' ) => 'dotted',
						__( 'Double', 'porto-functionality' ) => 'double',
						__( 'Inset', 'porto-functionality' )  => 'inset',
						__( 'Outset', 'porto-functionality' ) => 'outset',
					),
					'selectors'  => array(
						'{{WRAPPER}} .toggle' => 'border-style: {{VALUE}};',
					),
					'group'      => __( 'Faq', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Border Width', 'porto-functionality' ),
					'param_name' => 'faq_bw',
					'units'      => array( 'px' ),
					'dependency' => array(
						'element'            => 'faq_bs',
						'value_not_equal_to' => array( '', 'none' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .toggle' => 'border-width: {{VALUE}}{{UNIT}};',
					),
					'group'      => __( 'Faq', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'faq_item',
					'text'       => __( 'Normal', 'porto-functionality' ),
					'dependency' => array(
						'element'            => 'faq_bs',
						'value_not_equal_to' => array( '', 'none' ),
					),
					'group'      => __( 'Faq', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Border Color', 'porto-functionality' ),
					'param_name' => 'faq_bc',
					'dependency' => array(
						'element'            => 'faq_bs',
						'value_not_equal_to' => array( '', 'none' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .toggle' => 'border-color: {{VALUE}};',
					),
					'group'      => __( 'Faq', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'faq_item_active',
					'text'       => __( 'Active', 'porto-functionality' ),
					'dependency' => array(
						'element'            => 'faq_bs',
						'value_not_equal_to' => array( '', 'none' ),
					),
					'group'      => __( 'Faq', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Border Color', 'porto-functionality' ),
					'param_name' => 'faq_bc_active',
					'dependency' => array(
						'element'            => 'faq_bs',
						'value_not_equal_to' => array( '', 'none' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .toggle.active' => 'border-color: {{VALUE}};',
					),
					'group'      => __( 'Faq', 'porto-functionality' ),
				),

				array(
					'type'        => 'porto_typography',
					'heading'     => __( 'Typography', 'porto-functionality' ),
					'param_name'  => 'title_tg',
					'selectors'   => array(
						'{{WRAPPER}} .toggle > label',
					),
					'qa_selector' => '.faq-row>.faq:nth-child(2) .toggle > label',
					'group'       => __( 'Title', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Padding', 'porto-functionality' ),
					'param_name' => 'title_pd',
					'value'      => '',
					'selectors'  => array(
						'{{WRAPPER}} .toggle > label' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'group'      => __( 'Title', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Border Style', 'porto-functionality' ),
					'param_name' => 'title_bs',
					'std'        => '',
					'value'      => array(
						__( 'Default', 'porto-functionality' )   => '',
						__( 'None', 'porto-functionality' )   => 'none',
						__( 'Solid', 'porto-functionality' )  => 'solid',
						__( 'Dashed', 'porto-functionality' ) => 'dashed',
						__( 'Dotted', 'porto-functionality' ) => 'dotted',
						__( 'Double', 'porto-functionality' ) => 'double',
						__( 'Inset', 'porto-functionality' )  => 'inset',
						__( 'Outset', 'porto-functionality' ) => 'outset',
					),
					'selectors'  => array(
						'{{WRAPPER}} .toggle > label' => 'border-style: {{VALUE}};',
					),
					'group'      => __( 'Title', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Border Width', 'porto-functionality' ),
					'param_name' => 'title_bw',
					'units'      => array( 'px' ),
					'dependency' => array(
						'element'            => 'title_bs',
						'value_not_equal_to' => array( '', 'none' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .toggle > label' => 'border-width: {{VALUE}}{{UNIT}};',
					),
					'group'      => __( 'Title', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'faq_title',
					'text'       => __( 'Normal', 'porto-functionality' ),
					'group'      => __( 'Title', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Background Color', 'porto-functionality' ),
					'param_name' => 'title_bgc',
					'selectors'  => array(
						'{{WRAPPER}} .toggle > label' => 'background-color: {{VALUE}};',
					),
					'group'      => __( 'Title', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Text Color', 'porto-functionality' ),
					'param_name' => 'title_clr',
					'selectors'  => array(
						'{{WRAPPER}} .toggle > label' => 'color: {{VALUE}};',
					),
					'group'      => __( 'Title', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Border Color', 'porto-functionality' ),
					'param_name' => 'title_bc',
					'dependency' => array(
						'element'            => 'title_bs',
						'value_not_equal_to' => array( '', 'none' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .toggle > label' => 'border-color: {{VALUE}};',
					),
					'group'      => __( 'Title', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Border Radius', 'porto-functionality' ),
					'param_name' => 'title_br',
					'value'      => '',
					'selectors'  => array(
						'{{WRAPPER}} .toggle > label' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'group'      => __( 'Title', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'faq_title_hover',
					'text'       => __( 'Hover', 'porto-functionality' ),
					'group'      => __( 'Title', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Background Color', 'porto-functionality' ),
					'param_name' => 'title_bgc_hover',
					'selectors'  => array(
						'{{WRAPPER}} .toggle > label:hover' => 'background-color: {{VALUE}};',
					),
					'group'      => __( 'Title', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Text Color', 'porto-functionality' ),
					'param_name' => 'title_clr_hover',
					'selectors'  => array(
						'{{WRAPPER}} .toggle > label:hover' => 'color: {{VALUE}};',
					),
					'group'      => __( 'Title', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Border Color', 'porto-functionality' ),
					'param_name' => 'title_bc_hover',
					'dependency' => array(
						'element'            => 'title_bs',
						'value_not_equal_to' => array( '', 'none' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .toggle > label:hover' => 'border-color: {{VALUE}};',
					),
					'group'      => __( 'Title', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Border Radius', 'porto-functionality' ),
					'param_name' => 'title_br_hover',
					'value'      => '',
					'selectors'  => array(
						'{{WRAPPER}} .toggle > label:hover' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'group'      => __( 'Title', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'faq_title_active',
					'text'       => __( 'Active', 'porto-functionality' ),
					'group'      => __( 'Title', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Background Color', 'porto-functionality' ),
					'param_name' => 'title_bgc_active',
					'selectors'  => array(
						'{{WRAPPER}} .toggle.active > label' => 'background-color: {{VALUE}};',
					),
					'group'      => __( 'Title', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Text Color', 'porto-functionality' ),
					'param_name' => 'title_clr_active',
					'selectors'  => array(
						'{{WRAPPER}} .toggle.active > label' => 'color: {{VALUE}};',
					),
					'group'      => __( 'Title', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Border Color', 'porto-functionality' ),
					'param_name' => 'title_bc_active',
					'dependency' => array(
						'element'            => 'title_bs',
						'value_not_equal_to' => array( '', 'none' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .toggle.active > label' => 'border-color: {{VALUE}};',
					),
					'group'      => __( 'Title', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Border Radius', 'porto-functionality' ),
					'param_name' => 'title_br_active',
					'value'      => '',
					'selectors'  => array(
						'{{WRAPPER}} .toggle.active > label' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'group'      => __( 'Title', 'porto-functionality' ),
				),

				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Right Spacing', 'porto-functionality' ),
					'param_name' => 'toggle_rs',
					'units'      => array( 'px', 'rem', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .toggle > label:before' => ( is_rtl() ? 'left' : 'right' ) . ': {{VALUE}}{{UNIT}};',
					),
					'group'      => __( 'Toggle Icon', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'toggle_style',
					'text'       => __( 'Normal', 'porto-functionality' ),
					'group'      => __( 'Toggle Icon', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Icon Type', 'porto-functionality' ),
					'param_name' => 'toggle_icon',
					'std'        => '',
					'value'      => array(
						__( 'Custom', 'porto-functionality' ) => '',
						__( 'Porto', 'porto-functionality' ) => 'porto',
						__( 'Font Awesome 5', 'porto-functionality' ) => 'Font Awesome 5 Free',
						__( 'Simple Line Icons', 'porto-functionality' ) => 'Simple-Line-Icons',
					),
					'selectors'  => array(
						'{{WRAPPER}} .toggle > label:before' => 'font-family: "{{VALUE}}";',
					),
					'group'      => __( 'Toggle Icon', 'porto-functionality' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Icon Text', 'porto-functionality' ),
					'description' => __( 'Please input css content value which will be used as toggle icon.', 'porto-functionality' ),
					'param_name'  => 'toggle_content',
					'selectors'   => array(
						'{{WRAPPER}} .toggle > label:before' => 'content: "{{VALUE}}"; border: none; line-height: 1; top: 50%;',
					),
					'dependency'  => array(
						'element' => 'toggle_icon',
						'value'   => array( '' ),
					),
					'group'       => __( 'Toggle Icon', 'porto-functionality' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Icon Text', 'porto-functionality' ),
					'description' => __( 'Please input css content value without "\" prefix.', 'porto-functionality' ),
					'param_name'  => 'toggle_content1',
					'selectors'   => array(
						'{{WRAPPER}} .toggle > label:before' => 'content: "\\\{{VALUE}}"; border: none; line-height: 1; top: 50%;',
					),
					'dependency'  => array(
						'element'            => 'toggle_icon',
						'value_not_equal_to' => array( '' ),
					),
					'group'       => __( 'Toggle Icon', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Font Size', 'porto-functionality' ),
					'param_name' => 'toggle_fs',
					'units'      => array( 'px', 'rem', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .toggle > label:before' => 'font-size: {{VALUE}}{{UNIT}};',
					),
					'group'      => __( 'Toggle Icon', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Font Weight', 'porto-functionality' ),
					'param_name' => 'toggle_fw',
					'std'        => '',
					'value'      => array(
						__( 'Default', 'porto-functionality' ) => '',
						'300' => '300',
						'400' => '400',
						'500' => '500',
						'600' => '600',
						'700' => '700',
						'800' => '800',
					),
					'selectors'  => array(
						'{{WRAPPER}} .toggle > label:before' => 'font-weight: {{VALUE}};',
					),
					'group'      => __( 'Toggle Icon', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Toggle Color', 'porto-functionality' ),
					'param_name' => 'toggle_clr',
					'selectors'  => array(
						'{{WRAPPER}} .toggle > label:before' => 'color: {{VALUE}}; border-color: {{VALUE}};',
					),
					'group'      => __( 'Toggle Icon', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Rotate', 'porto-functionality' ),
					'param_name' => 'toggle_rotate',
					'units'      => array( 'deg' ),
					'selectors'  => array(
						'{{WRAPPER}} .toggle > label:before' => 'transform: translate3d(0, -50%, 0) rotate({{VALUE}}{{UNIT}});',
					),
					'group'      => __( 'Toggle Icon', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'toggle_style_active',
					'text'       => __( 'Active', 'porto-functionality' ),
					'group'      => __( 'Toggle Icon', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Icon Type', 'porto-functionality' ),
					'param_name' => 'toggle_icon_active',
					'std'        => '',
					'value'      => array(
						__( 'Custom', 'porto-functionality' ) => '',
						__( 'Porto', 'porto-functionality' ) => 'porto',
						__( 'Font Awesome 5', 'porto-functionality' ) => 'Font Awesome 5 Free',
						__( 'Simple Line Icons', 'porto-functionality' ) => 'Simple-Line-Icons',
					),
					'selectors'  => array(
						'{{WRAPPER}} .toggle.active > label:before' => 'font-family: "{{VALUE}}";',
					),
					'group'      => __( 'Toggle Icon', 'porto-functionality' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Icon Text', 'porto-functionality' ),
					'description' => __( 'Please input css content value which will be used as toggle icon.', 'porto-functionality' ),
					'param_name'  => 'toggle_content_active',
					'selectors'   => array(
						'{{WRAPPER}} .toggle.active > label:before' => 'content: "{{VALUE}}"; border: none; line-height: 1; top: 50%;',
					),
					'group'       => __( 'Toggle Icon', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Font Size', 'porto-functionality' ),
					'param_name' => 'toggle_fs_active',
					'units'      => array( 'px', 'rem', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .toggle.active > label:before' => 'font-size: {{VALUE}}{{UNIT}};',
					),
					'group'      => __( 'Toggle Icon', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Font Weight', 'porto-functionality' ),
					'param_name' => 'toggle_fw_active',
					'std'        => '',
					'value'      => array(
						__( 'Default', 'porto-functionality' ) => '',
						'300' => '300',
						'400' => '400',
						'500' => '500',
						'600' => '600',
						'700' => '700',
						'800' => '800',
					),
					'selectors'  => array(
						'{{WRAPPER}} .toggle.active > label:before' => 'font-weight: {{VALUE}};',
					),
					'group'      => __( 'Toggle Icon', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Toggle Color', 'porto-functionality' ),
					'param_name' => 'toggle_clr_active',
					'selectors'  => array(
						'{{WRAPPER}} .toggle.active > label:before' => 'color: {{VALUE}}; border-color: {{VALUE}};',
					),
					'group'      => __( 'Toggle Icon', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Rotate', 'porto-functionality' ),
					'param_name' => 'toggle_rotate_active',
					'units'      => array( 'deg' ),
					'selectors'  => array(
						'{{WRAPPER}} .toggle.active > label:before' => 'transform: translate3d(0, -50%, 0) rotate({{VALUE}}{{UNIT}});',
					),
					'group'      => __( 'Toggle Icon', 'porto-functionality' ),
				),

				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Typography', 'porto-functionality' ),
					'param_name' => 'content_tg',
					'selectors'  => array(
						'{{WRAPPER}} .toggle-content,{{WRAPPER}} .toggle-content >p',
					),
					'group'      => __( 'Content', 'porto-functionality' ),
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Text Color', 'porto-functionality' ),
					'param_name'  => 'content_clr',
					'selectors'   => array(
						'{{WRAPPER}} .toggle-content' => 'color: {{VALUE}};',
					),
					'qa_selector' => '.toggle-content',
					'group'       => __( 'Content', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Padding', 'porto-functionality' ),
					'param_name' => 'content_pd',
					'value'      => '',
					'selectors'  => array(
						'{{WRAPPER}} .toggle-content' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'group'      => __( 'Content', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Border Radius', 'porto-functionality' ),
					'param_name' => 'content_br',
					'value'      => '',
					'selectors'  => array(
						'{{WRAPPER}} .toggle-content' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'group'      => __( 'Content', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Background Color', 'porto-functionality' ),
					'param_name' => 'content_bgc',
					'selectors'  => array(
						'{{WRAPPER}} .toggle-content' => 'background-color: {{VALUE}};',
					),
					'group'      => __( 'Content', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Border Style', 'porto-functionality' ),
					'param_name' => 'content_bs',
					'std'        => '',
					'value'      => array(
						__( 'Default', 'porto-functionality' ) => '',
						__( 'None', 'porto-functionality' )   => 'none',
						__( 'Solid', 'porto-functionality' )  => 'solid',
						__( 'Dashed', 'porto-functionality' ) => 'dashed',
						__( 'Dotted', 'porto-functionality' ) => 'dotted',
						__( 'Double', 'porto-functionality' ) => 'double',
						__( 'Inset', 'porto-functionality' )  => 'inset',
						__( 'Outset', 'porto-functionality' ) => 'outset',
					),
					'selectors'  => array(
						'{{WRAPPER}} .toggle-content' => 'border-style: {{VALUE}};',
					),
					'group'      => __( 'Content', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Border Width', 'porto-functionality' ),
					'param_name' => 'content_bw',
					'units'      => array( 'px' ),
					'dependency' => array(
						'element'            => 'content_bs',
						'value_not_equal_to' => array( '', 'none' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .toggle-content' => 'border-width: {{VALUE}}{{UNIT}};',
					),
					'group'      => __( 'Content', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Border Color', 'porto-functionality' ),
					'param_name' => 'content_bc',
					'dependency' => array(
						'element'            => 'content_bs',
						'value_not_equal_to' => array( '', 'none' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .toggle-content' => 'border-color: {{VALUE}};',
					),
					'group'      => __( 'Content', 'porto-functionality' ),
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Faqs' ) ) {
		class WPBakeryShortCode_Porto_Faqs extends WPBakeryShortCode {
		}
	}
}
