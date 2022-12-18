<?php

// Porto Carousel
add_action( 'vc_after_init', 'porto_load_carousel_shortcode' );

function porto_load_carousel_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'            => 'Porto ' . __( 'Carousel', 'porto-functionality' ),
			'base'            => 'porto_carousel',
			'category'        => __( 'Porto', 'porto-functionality' ),
			'description'     => __( 'A multiple page slider', 'porto-functionality' ),
			'icon'            => 'fas fa-ellipsis-h',
			'as_parent'       => array( 'except' => 'porto_carousel' ),
			'content_element' => true,
			'controls'        => 'full',
			//'is_container' => true,
			'js_view'         => 'VcColumnView',
			'params'          => array(
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Stage Padding', 'porto-functionality' ),
					'param_name' => 'stage_padding',
					'value'      => 40,
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show items in stage padding', 'porto-functionality' ),
					'param_name' => 'show_items_padding',
					'dependency' => array(
						'element'   => 'stage_padding',
						'not_empty' => true,
					),
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Disable Mouse Drag', 'porto-functionality' ),
					'description' => __( 'This option will disapprove Mouse Drag.', 'porto-functionality' ),
					'param_name'  => 'disable_mouse_drag',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Item Margin (px)', 'porto-functionality' ),
					'param_name' => 'margin',
					'value'      => 10,
					'selectors'  => array(
						'{{WRAPPER}}' => '--porto-el-spacing: {{VALUE}}px;',
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Auto Play', 'porto-functionality' ),
					'param_name' => 'autoplay',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Auto Play Timeout', 'porto-functionality' ),
					'param_name' => 'autoplay_timeout',
					'dependency' => array(
						'element'   => 'autoplay',
						'not_empty' => true,
					),
					'value'      => 5000,
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Pause on Mouse Hover', 'porto-functionality' ),
					'param_name' => 'autoplay_hover_pause',
					'dependency' => array(
						'element'   => 'autoplay',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Items', 'porto-functionality' ),
					'param_name' => 'items_responsive',
					'responsive' => true,
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Nav', 'porto-functionality' ),
					'param_name' => 'show_nav',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'group'      => __( 'Navigation', 'porto-functionality' ),
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
					'group'      => __( 'Navigation', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Nav Position', 'porto-functionality' ),
					'param_name' => 'nav_pos',
					'value'      => array(
						__( 'Middle', 'porto-functionality' ) => '',
						__( 'Middle Inside', 'porto-functionality' ) => 'nav-pos-inside',
						__( 'Middle Outside', 'porto-functionality' ) => 'nav-pos-outside',
						__( 'Top', 'porto-functionality' ) => 'show-nav-title',
						__( 'Bottom', 'porto-functionality' ) => 'nav-bottom',
						__( 'Custom', 'porto-functionality' ) => 'custom-pos',
					),
					'dependency' => array(
						'element'   => 'show_nav',
						'not_empty' => true,
					),
					'group'      => __( 'Navigation', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Nav Type', 'porto-functionality' ),
					'param_name' => 'nav_type',
					'value'      => porto_sh_commons( 'carousel_nav_types' ),
					'dependency' => array(
						'element' => 'nav_pos',
						'value'   => array( '', 'nav-pos-inside', 'nav-pos-outside', 'nav-bottom', 'custom-pos' ),
					),
					'group'      => __( 'Navigation', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Dots', 'porto-functionality' ),
					'param_name' => 'show_dots',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'group'      => __( 'Navigation', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Dots Position', 'porto-functionality' ),
					'param_name' => 'dots_pos',
					'value'      => array(
						__( 'Outside', 'porto-functionality' )          => '',
						__( 'Inside', 'porto-functionality' )           => 'nav-inside',
						__( 'Top beside title', 'porto-functionality' ) => 'show-dots-title',
						__( 'Custom', 'porto-functionality' )           => 'custom-dots',
					),
					'dependency' => array(
						'element'   => 'show_dots',
						'not_empty' => true,
					),
					'group'      => __( 'Navigation', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Dots Style', 'porto-functionality' ),
					'param_name' => 'dots_style',
					'value'      => array(
						__( 'Default', 'porto-functionality' ) => '',
						__( 'Circle inner dot', 'porto-functionality' ) => 'dots-style-1',
					),
					'dependency' => array(
						'element'   => 'show_dots',
						'not_empty' => true,
					),
					'group'      => __( 'Navigation', 'porto-functionality' ),
				),
				array(
					'type'        => 'porto_number',
					'heading'     => __( 'Top Position', 'porto-functionality' ),
					'description' => __( 'You should choose one from the "Top Position" and the "Bottom Position"', 'porto-functionality' ),
					'param_name'  => 'dots_pos_top',
					'units'       => array( 'px', 'rem', '%' ),
					'dependency'  => array(
						'element' => 'dots_pos',
						'value'   => 'custom-dots',
					),
					'responsive'  => true,
					'selectors'   => array(
						'{{WRAPPER}} .owl-dots' => 'top: {{VALUE}}{{UNIT}} !important;',
					),
					'qa_selector' => '.owl-dots > .owl-dot:first-child',
					'group'       => __( 'Dots Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'porto_number',
					'heading'     => __( 'Bottom Position', 'porto-functionality' ),
					'description' => __( 'You should choose one from the "Top Position" and the "Bottom Position"', 'porto-functionality' ),
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
					'type'       => 'dropdown',
					'heading'    => __( 'Dots Align', 'porto-functionality' ),
					'param_name' => 'dots_align',
					'value'      => array(
						__( 'Right', 'porto-functionality' )  => '',
						__( 'Center', 'porto-functionality' ) => 'nav-inside-center',
						__( 'Left', 'porto-functionality' )   => 'nav-inside-left',
					),
					'dependency' => array(
						'element' => 'dots_pos',
						'value'   => array( 'nav-inside' ),
					),
					'group'      => __( 'Navigation', 'porto-functionality' ),
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
					'type'       => 'porto_animation_type',
					'heading'    => __( 'Item Animation In', 'porto-functionality' ),
					'param_name' => 'animate_in',
					'group'      => __( 'Animation', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_animation_type',
					'heading'    => __( 'Item Animation Out', 'porto-functionality' ),
					'param_name' => 'animate_out',
					'group'      => __( 'Animation', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Infinite loop', 'porto-functionality' ),
					'param_name' => 'loop',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'group'      => __( 'Advanced', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Full Screen', 'porto-functionality' ),
					'param_name' => 'fullscreen',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'group'      => __( 'Advanced', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Center Item', 'porto-functionality' ),
					'param_name' => 'center',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'group'      => __( 'Advanced', 'porto-functionality' ),
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Fetch Videos', 'porto-functionality' ),
					'param_name'  => 'video',
					'value'       => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'description' => __( 'Please edit video items using porto carousel item element.', 'porto-functionality' ),
					'group'       => __( 'Advanced', 'porto-functionality' ),
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Lazy Load', 'porto-functionality' ),
					'param_name'  => 'lazyload',
					'value'       => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'description' => __( 'Please edit lazy load images using porto carousel item element or porto interactive banner element.', 'porto-functionality' ),
					'group'       => __( 'Advanced', 'porto-functionality' ),
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Merge Items', 'porto-functionality' ),
					'param_name'  => 'merge',
					'value'       => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'description' => __( 'Please edit merge items using porto carousel item element.', 'porto-functionality' ),
					'group'       => __( 'Advanced', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Merge Fit', 'porto-functionality' ),
					'param_name' => 'mergeFit',
					'std'        => 'yes',
					'dependency' => array(
						'element'   => 'merge',
						'not_empty' => true,
					),
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'group'      => __( 'Advanced', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Merge Fit on Desktop', 'porto-functionality' ),
					'param_name' => 'mergeFit_lg',
					'std'        => 'yes',
					'dependency' => array(
						'element'   => 'merge',
						'not_empty' => true,
					),
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'group'      => __( 'Advanced', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Merge Fit on Tablet', 'porto-functionality' ),
					'param_name' => 'mergeFit_md',
					'std'        => 'yes',
					'dependency' => array(
						'element'   => 'merge',
						'not_empty' => true,
					),
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'group'      => __( 'Advanced', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Merge Fit on Mobile', 'porto-functionality' ),
					'param_name' => 'mergeFit_sm',
					'std'        => 'yes',
					'dependency' => array(
						'element'   => 'merge',
						'not_empty' => true,
					),
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'group'      => __( 'Advanced', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Merge Fit on Mini', 'porto-functionality' ),
					'param_name' => 'mergeFit_xs',
					'std'        => 'yes',
					'dependency' => array(
						'element'   => 'merge',
						'not_empty' => true,
					),
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'group'      => __( 'Advanced', 'porto-functionality' ),
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Carousel' ) ) {
		class WPBakeryShortCode_Porto_Carousel extends WPBakeryShortCodesContainer {
		}
	}
}
