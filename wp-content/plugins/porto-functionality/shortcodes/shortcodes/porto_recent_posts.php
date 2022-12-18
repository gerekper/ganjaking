<?php

// Porto Recent Posts
add_action( 'vc_after_init', 'porto_load_recent_posts_shortcode' );

function porto_load_recent_posts_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Recent Posts', 'porto-functionality' ),
			'base'        => 'porto_recent_posts',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Show posts by slider', 'porto-functionality' ),
			'icon'        => 'porto-sc Simple-Line-Icons-docs',
			'params'      => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Title', 'porto-functionality' ),
					'param_name'  => 'title',
					'admin_label' => true,
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'View Type', 'porto-functionality' ),
					'param_name' => 'view',
					'std'        => '',
					'value'      => array(
						__( 'Standard', 'porto-functionality' )   => '',
						__( 'Read More Link', 'porto-functionality' ) => 'style-1',
						__( 'Post Meta', 'porto-functionality' )  => 'style-2',
						__( 'Read More Button', 'porto-functionality' ) => 'style-3',
						__( 'Side Image', 'porto-functionality' ) => 'style-4',
						__( 'Post Cats', 'porto-functionality' )  => 'style-5',
						__( 'Post Author with photo', 'porto-functionality' ) => 'style-7',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Author Name', 'porto-functionality' ),
					'param_name' => 'author',
					'dependency' => array(
						'element' => 'view',
						'value'   => array( 'style-1', 'style-3' ),
					),
					'std'        => '',
					'value'      => array(
						__( 'Standard', 'porto-functionality' ) => '',
						__( 'Show', 'porto-functionality' ) => 'show',
						__( 'Hide', 'porto-functionality' ) => 'hide',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Button Style', 'porto-functionality' ),
					'param_name' => 'btn_style',
					'dependency' => array(
						'element' => 'view',
						'value'   => array( 'style-3' ),
					),
					'std'        => '',
					'value'      => array(
						__( 'Standard', 'porto-functionality' ) => '',
						__( 'Normal', 'porto-functionality' ) => 'btn-normal',
						__( 'Borders', 'porto-functionality' ) => 'btn-borders',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Button Size', 'porto-functionality' ),
					'param_name' => 'btn_size',
					'dependency' => array(
						'element' => 'view',
						'value'   => array( 'style-3' ),
					),
					'std'        => '',
					'value'      => array(
						__( 'Standard', 'porto-functionality' ) => '',
						__( 'Normal', 'porto-functionality' ) => 'btn-normal',
						__( 'Small', 'porto-functionality' )  => 'btn-sm',
						__( 'Extra Small', 'porto-functionality' ) => 'btn-xs',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Button Color', 'porto-functionality' ),
					'param_name' => 'btn_color',
					'dependency' => array(
						'element' => 'view',
						'value'   => array( 'style-3' ),
					),
					'std'        => '',
					'value'      => array(
						__( 'Standard', 'porto-functionality' ) => '',
						__( 'Default', 'porto-functionality' ) => 'btn-default',
						__( 'Primary', 'porto-functionality' ) => 'btn-primary',
						__( 'Secondary', 'porto-functionality' ) => 'btn-secondary',
						__( 'Tertiary', 'porto-functionality' ) => 'btn-tertiary',
						__( 'Quaternary', 'porto-functionality' ) => 'btn-quaternary',
						__( 'Dark', 'porto-functionality' )  => 'btn-dark',
						__( 'Light', 'porto-functionality' ) => 'btn-light',
					),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Posts Count', 'porto-functionality' ),
					'param_name'  => 'number',
					'value'       => '8',
					'admin_label' => true,
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Category IDs', 'porto-functionality' ),
					'param_name'  => 'cats',
					'admin_label' => true,
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Post Image', 'porto-functionality' ),
					'param_name' => 'show_image',
					'std'        => 'yes',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'dependency' => array(
						'element'            => 'view',
						'value_not_equal_to' => 'style-7',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Image Size', 'porto-functionality' ),
					'param_name' => 'image_size',
					'value'      => porto_sh_commons( 'image_sizes' ),
					'std'        => '',
					'dependency' => array(
						'element'   => 'show_image',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Post Metas', 'porto-functionality' ),
					'param_name' => 'show_metas',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'std'        => 'yes',
					'dependency' => array(
						'element' => 'view',
						'value'   => array( '', 'style-2', 'style-4', 'style-5' ),
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Excerpt Length', 'porto-functionality' ),
					'param_name' => 'excerpt_length',
					'value'      => '20',
				),
				array(
					'type'       => 'number',
					'heading'    => __( 'Column Spacing (px)', 'porto-functionality' ),
					'param_name' => 'spacing',
					'min'        => 0,
					'max'        => 60,
					'step'       => 1,
					'selectors'  => array(
						'{{WRAPPER}}' => '--porto-el-spacing: {{VALUE}}px;',
					),
				),
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'description_items',
					'text'       => esc_html__( 'Responsive Items', 'porto-functionality' ),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Items to show on Large Desktop', 'porto-functionality' ),
					'param_name' => 'items',
					'value'      => '',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Items to show on Desktop', 'porto-functionality' ),
					'param_name' => 'items_desktop',
					'value'      => '4',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Items to show on Tablets', 'porto-functionality' ),
					'param_name' => 'items_tablets',
					'value'      => '3',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Items to show on Mobile', 'porto-functionality' ),
					'param_name' => 'items_mobile',
					'value'      => '2',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Items Row', 'porto-functionality' ),
					'param_name' => 'items_row',
					'value'      => '1',
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Change Slider Config', 'porto-functionality' ),
					'param_name' => 'slider_config',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'group'      => __( 'Slider Options', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Nav', 'porto-functionality' ),
					'param_name' => 'show_nav',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'dependency' => array(
						'element'   => 'slider_config',
						'not_empty' => true,
					),
					'group'      => __( 'Slider Options', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Nav Position', 'porto-functionality' ),
					'param_name' => 'nav_pos',
					'value'      => array(
						__( 'Middle', 'porto-functionality' ) => '',
						__( 'Top', 'porto-functionality' ) => 'show-nav-title',
						__( 'Bottom', 'porto-functionality' ) => 'nav-bottom',
						__( 'Custom', 'porto-functionality' ) => 'custom-pos',
					),
					'dependency' => array(
						'element'   => 'show_nav',
						'not_empty' => true,
					),
					'group'      => __( 'Slider Options', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Nav Type', 'porto-functionality' ),
					'param_name' => 'nav_type',
					'value'      => porto_sh_commons( 'carousel_nav_types' ),
					'dependency' => array(
						'element' => 'nav_pos',
						'value'   => array( '', 'nav-bottom', 'custom-pos' ),
					),
					'group'      => __( 'Slider Options', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Nav on Hover', 'porto-functionality' ),
					'param_name' => 'show_nav_hover',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'dependency' => array(
						'element'   => 'show_nav',
						'not_empty' => true,
					),
					'group'      => __( 'Slider Options', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Dots', 'porto-functionality' ),
					'param_name' => 'show_dots',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'dependency' => array(
						'element'   => 'slider_config',
						'not_empty' => true,
					),
					'group'      => __( 'Slider Options', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Dots Position', 'porto-functionality' ),
					'param_name' => 'dots_pos',
					'std'        => '',
					'value'      => array(
						__( 'Bottom', 'porto-functionality' )           => '',
						__( 'Top beside title', 'porto-functionality' ) => 'show-dots-title',
						__( 'Custom', 'porto-functionality' )           => 'custom-dots',
					),
					'dependency' => array(
						'element'   => 'show_dots',
						'not_empty' => true,
					),
					'group'      => __( 'Slider Options', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Dots Style', 'porto-functionality' ),
					'param_name' => 'dots_style',
					'std'        => '',
					'value'      => array(
						__( 'Default', 'porto-functionality' ) => '',
						__( 'Circle inner dot', 'porto-functionality' ) => 'dots-style-1',
					),
					'dependency' => array(
						'element'   => 'show_dots',
						'not_empty' => true,
					),
					'group'      => __( 'Slider Options', 'porto-functionality' ),
				),
				array(
					'type'        => 'porto_number',
					'heading'     => __( 'Top Position', 'porto-functionality' ),
					'description' => __( 'You should choose one from the "Top Position" and the "Bottom Position".', 'porto-functionality' ),
					'param_name'  => 'dots_pos_top',
					'units'       => array( 'px', 'rem', '%' ),
					'dependency'  => array(
						'element' => 'dots_pos',
						'value'   => 'custom-dots',
					),
					'responsive'  => true,
					'separator'   => 'before',
					'selectors'   => array(
						'{{WRAPPER}} .owl-dots' => 'top: {{VALUE}}{{UNIT}} !important;',
					),
					'qa_selector' => '.owl-dots > .owl-dot:first-child',
					'group'       => __( 'Dots Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'porto_number',
					'heading'     => __( 'Bottom Position', 'porto-functionality' ),
					'description' => __( 'You should choose one from the "Top Position" and the "Bottom Position".', 'porto-functionality' ),
					'param_name'  => 'dots_pos_bottom',
					'units'       => array( 'px', 'rem', '%' ),
					'dependency'  => array(
						'element' => 'dots_pos',
						'value'   => 'custom-dots',
					),
					'responsive'  => true,
					'selectors'   => array(
						'{{WRAPPER}} .owl-dots' => 'bottom: {{VALUE}}{{UNIT}} !important;',
					),
					'group'       => __( 'Dots Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'porto_number',
					'heading'     => __( 'Left Position', 'porto-functionality' ),
					'description' => __( 'You should choose one from the "Left Position" and the "Right Position".', 'porto-functionality' ),
					'param_name'  => 'dots_pos_left',
					'units'       => array( 'px', 'rem', '%' ),
					'dependency'  => array(
						'element' => 'dots_pos',
						'value'   => 'custom-dots',
					),
					'responsive'  => true,
					'selectors'   => array(
						'{{WRAPPER}} .owl-dots' => 'left: {{VALUE}}{{UNIT}} !important;',
					),
					'group'       => __( 'Dots Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'porto_number',
					'heading'     => __( 'Right Position', 'porto-functionality' ),
					'description' => __( 'You should choose one from the "Left Position" and the "Right Position".', 'porto-functionality' ),
					'param_name'  => 'dots_pos_right',
					'units'       => array( 'px', 'rem', '%' ),
					'dependency'  => array(
						'element' => 'dots_pos',
						'value'   => 'custom-dots',
					),
					'responsive'  => true,
					'selectors'   => array(
						'{{WRAPPER}} .owl-dots' => 'right: {{VALUE}}{{UNIT}} !important;',
					),
					'group'       => __( 'Dots Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'dots_br_color',
					'heading'    => __( 'Dots Color', 'porto-functionality' ),
					'separator'  => 'before',
					'dependency' => array(
						'element' => 'dots_style',
						'value'   => 'dots-style-1',
					),
					'selectors'  => array(
						'{{WRAPPER}} .owl-dot span' => 'border-color: {{VALUE}} !important;',
					),
					'group'      => __( 'Dots Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'dots_abr_color',
					'heading'    => __( 'Dots Active Color', 'porto-functionality' ),
					'dependency' => array(
						'element' => 'dots_style',
						'value'   => 'dots-style-1',
					),
					'selectors'  => array(
						'{{WRAPPER}} .owl-dot.active span, {{WRAPPER}} .owl-dot:hover span' => 'color: {{VALUE}} !important; border-color: {{VALUE}} !important;',
					),
					'group'      => __( 'Dots Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'dots_bg_color',
					'heading'    => __( 'Dots Color', 'porto-functionality' ),
					'separator'  => 'before',
					'dependency' => array(
						'element'            => 'dots_style',
						'value_not_equal_to' => 'dots-style-1',
					),
					'selectors'  => array(
						'{{WRAPPER}} .owl-dot span' => 'background-color: {{VALUE}} !important;',
					),
					'group'      => __( 'Dots Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'dots_abg_color',
					'heading'    => __( 'Dots Active Color', 'porto-functionality' ),
					'dependency' => array(
						'element'            => 'dots_style',
						'value_not_equal_to' => 'dots-style-1',
					),
					'selectors'  => array(
						'{{WRAPPER}} .owl-dot.active span, {{WRAPPER}} .owl-dot:hover span' => 'background-color: {{VALUE}} !important;',
					),
					'group'      => __( 'Dots Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'porto_number',
					'heading'     => __( 'Nav Font Size', 'porto-functionality' ),
					'param_name'  => 'nav_fs',
					'dependency'  => array(
						'element'   => 'show_nav',
						'not_empty' => true,
					),
					'separator'   => 'before',
					'selectors'   => array(
						'{{WRAPPER}} .owl-nav button' => 'font-size: {{VALUE}}px !important;',
					),
					'qa_selector' => '.owl-nav > .owl-prev',
					'group'       => __( 'Nav Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Nav Width', 'porto-functionality' ),
					'param_name' => 'nav_width',
					'units'      => array( 'px', 'rem', '%' ),
					'dependency' => array(
						'element' => 'nav_type',
						'value'   => array( '', 'rounded-nav', 'big-nav', 'nav-style-3' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .owl-nav button' => 'width: {{VALUE}}{{UNIT}} !important;',
					),
					'group'      => __( 'Nav Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Nav Height', 'porto-functionality' ),
					'param_name' => 'nav_height',
					'units'      => array( 'px', 'rem', '%' ),
					'dependency' => array(
						'element' => 'nav_type',
						'value'   => array( '', 'rounded-nav', 'big-nav', 'nav-style-3' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .owl-nav button' => 'height: {{VALUE}}{{UNIT}} !important;',
					),
					'group'      => __( 'Nav Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Border Radius', 'porto-functionality' ),
					'param_name' => 'nav_br',
					'units'      => array( 'px', '%' ),
					'dependency' => array(
						'element' => 'nav_type',
						'value'   => array( '', 'rounded-nav', 'big-nav', 'nav-style-3' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .owl-nav button' => 'border-radius: {{VALUE}}{{UNIT}} !important;',
					),
					'group'      => __( 'Nav Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Horizontal Position', 'porto-functionality' ),
					'param_name' => 'nav_h_pos',
					'units'      => array( 'px', 'rem', '%' ),
					'dependency' => array(
						'element' => 'nav_pos',
						'value'   => array( 'custom-pos', 'show-nav-title' ),
					),
					'responsive' => true,
					'selectors'  => array(
						'{{WRAPPER}} .owl-nav button.owl-prev' => 'left: {{VALUE}}{{UNIT}} !important;',
						'{{WRAPPER}} .owl-nav button.owl-next' => 'right: {{VALUE}}{{UNIT}} !important;',
					),
					'group'      => __( 'Nav Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Vertical Position', 'porto-functionality' ),
					'param_name' => 'nav_v_pos',
					'units'      => array( 'px', 'rem', '%' ),
					'dependency' => array(
						'element' => 'nav_pos',
						'value'   => array( 'custom-pos', 'show-nav-title' ),
					),
					'responsive' => true,
					'selectors'  => array(
						'{{WRAPPER}} .owl-nav' => 'top: {{VALUE}}{{UNIT}} !important;',
					),
					'group'      => __( 'Nav Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'nav_color',
					'heading'    => __( 'Nav Color', 'porto-functionality' ),
					'dependency' => array(
						'element'   => 'show_nav',
						'not_empty' => true,
					),
					'separator'  => 'before',
					'selectors'  => array(
						'{{WRAPPER}} .owl-nav button' => 'color: {{VALUE}} !important;',
					),
					'group'      => __( 'Nav Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'nav_h_color',
					'heading'    => __( 'Hover Nav Color', 'porto-functionality' ),
					'dependency' => array(
						'element'   => 'show_nav',
						'not_empty' => true,
					),
					'selectors'  => array(
						'{{WRAPPER}} .owl-nav button:not(.disabled):hover' => 'color: {{VALUE}} !important;',
					),
					'group'      => __( 'Nav Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'nav_bg_color',
					'heading'    => __( 'Background Color', 'porto-functionality' ),
					'dependency' => array(
						'element' => 'nav_type',
						'value'   => array( '', 'big-nav', 'nav-style-3' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .owl-nav button' => 'background-color: {{VALUE}} !important;',
					),
					'group'      => __( 'Nav Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'nav_h_bg_color',
					'heading'    => __( 'Hover Background Color', 'porto-functionality' ),
					'dependency' => array(
						'element' => 'nav_type',
						'value'   => array( '', 'big-nav', 'nav-style-3' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .owl-nav button:not(.disabled):hover' => 'background-color: {{VALUE}} !important;',
					),
					'group'      => __( 'Nav Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'nav_br_color',
					'heading'    => __( 'Nav Border Color', 'porto-functionality' ),
					'dependency' => array(
						'element' => 'nav_type',
						'value'   => array( 'rounded-nav' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .owl-nav button' => 'border-color: {{VALUE}} !important;',
					),
					'group'      => __( 'Nav Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'nav_h_br_color',
					'heading'    => __( 'Hover Nav Border Color', 'porto-functionality' ),
					'dependency' => array(
						'element' => 'nav_type',
						'value'   => array( 'rounded-nav' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .owl-nav button:not(.disabled):hover' => 'border-color: {{VALUE}} !important;',
					),
					'group'      => __( 'Nav Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Title', 'porto-functionality' ),
					'param_name' => 'title_font',
					'selectors'  => array(
						'{{WRAPPER}} .post-slide .porto-post-title, {{WRAPPER}} h2, {{WRAPPER}} h3, {{WRAPPER}} h4, {{WRAPPER}} h5',
					),
					'group'      => __( 'Post Title', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'title_color',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}} .post-slide .porto-post-title a:not(:hover), {{WRAPPER}} h3 a:not(:hover), {{WRAPPER}} h4 a:not(:hover), {{WRAPPER}} h5 a:not(:hover), {{WRAPPER}} .post-title h2' => 'color: {{VALUE}} !important;',
						'{{WRAPPER}} a.text-dark:hover, {{WRAPPER}} .post-title:hover h2' => 'color: var(--porto-primary-color) !important;',
					),
					'group'      => __( 'Post Title', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Margin', 'porto-functionality' ),
					'param_name' => 'title_margin',
					'selectors'  => array(
						'{{WRAPPER}} .post-slide .porto-post-title, {{WRAPPER}} h2, {{WRAPPER}} h3, {{WRAPPER}} h4, {{WRAPPER}} h5' => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'group'      => __( 'Post Title', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Excerpt', 'porto-functionality' ),
					'param_name' => 'excerpt_font',
					'selectors'  => array(
						'{{WRAPPER}} .post-excerpt',
					),
					'group'      => __( 'Excerpt', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'excerpt_color',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}} .post-excerpt' => 'color: {{VALUE}};',
					),
					'group'      => __( 'Excerpt', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Margin', 'porto-functionality' ),
					'param_name' => 'excerpt_margin',
					'selectors'  => array(
						'{{WRAPPER}} .post-slide .post-excerpt' => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'group'      => __( 'Excerpt', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Meta', 'porto-functionality' ),
					'param_name' => 'meta_font',
					'selectors'  => array(
						'{{WRAPPER}} .post-slide .post-meta, {{WRAPPER}} .post-slide .style-4 .post-meta',
					),
					'group'      => __( 'Meta', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'meta_color',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}} .post-slide .post-meta, {{WRAPPER}} .post-meta h6' => 'color: {{VALUE}};',
					),
					'group'      => __( 'Meta', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'meta_link_color',
					'heading'    => __( 'Link Color', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}} .post-meta a:not(:hover)' => 'color: {{VALUE}};',
					),
					'group'      => __( 'Meta', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Margin', 'porto-functionality' ),
					'param_name' => 'meta_margin',
					'selectors'  => array(
						'{{WRAPPER}} .post-slide .post-meta' => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'group'      => __( 'Meta', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Button Typography', 'porto-functionality' ),
					'param_name' => 'read_more_font',
					'selectors'  => array(
						'{{WRAPPER}} .post-slide .btn, {{WRAPPER}} .read-more',
					),
					'group'      => __( 'Read More', 'porto-functionality' ),
					'dependency' => array(
						'element' => 'view',
						'value'   => array( 'style-1', 'style-3', 'style-4' ),
					),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'read_more_color',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}} .post-slide .btn:not(:hover), {{WRAPPER}} .read-more:not(:hover)' => 'color: {{VALUE}};',
					),
					'group'      => __( 'Read More', 'porto-functionality' ),
					'dependency' => array(
						'element' => 'view',
						'value'   => array( 'style-1', 'style-3', 'style-4' ),
					),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Margin', 'porto-functionality' ),
					'param_name' => 'read_more_margin',
					'selectors'  => array(
						'{{WRAPPER}} .post-slide .btn, {{WRAPPER}} .read-more' => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
						'{{WRAPPER}} .read-more:not(.btn)' => 'display: block;',
						'{{WRAPPER}} .post-slide .style-4 .read-more' => 'margin-top: {{TOP}};',
					),
					'group'      => __( 'Read More', 'porto-functionality' ),
					'dependency' => array(
						'element' => 'view',
						'value'   => array( 'style-1', 'style-3', 'style-4' ),
					),
				),

				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Recent_Posts' ) ) {
		class WPBakeryShortCode_Porto_Recent_Posts extends WPBakeryShortCode {
		}
	}
}
