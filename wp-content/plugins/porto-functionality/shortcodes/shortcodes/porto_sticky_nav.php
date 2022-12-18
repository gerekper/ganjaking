<?php

// Porto Sticky Nav
add_action( 'vc_after_init', 'porto_load_sticky_nav_shortcode' );

function porto_load_sticky_nav_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'            => 'Porto ' . __( 'Sticky Nav', 'porto-functionality' ),
			'base'            => 'porto_sticky_nav',
			'category'        => __( 'Porto', 'porto-functionality' ),
			'description'     => __( 'Sticky navigation', 'porto-functionality' ),
			'icon'            => 'porto_vc_sticky_nav',
			'as_parent'       => array( 'only' => 'porto_sticky_nav_link' ),
			'content_element' => true,
			'controls'        => 'full',
			//'is_container' => true,
			'js_view'         => 'VcColumnView',
			'params'          => array(
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'description_nav',
					'text'       => esc_html__( 'Please don\'t put this widget on sticky header.', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Wrap as Container', 'porto-functionality' ),
					'param_name' => 'container',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Min Width (unit: px)', 'porto-functionality' ),
					'param_name'  => 'min_width',
					'description' => __( 'Wll be disable sticky if window width is smaller than min width', 'porto-functionality' ),
					'value'       => 991,
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Fill the wrap', 'porto-functionality' ),
					'param_name' => 'fill_wrap',
					'selectors'  => array(
						'{{WRAPPER}} .nav li' => 'flex: 1; text-align: center;',
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Background Color', 'porto-functionality' ),
					'param_name' => 'bg_color',
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Border Bottom Width(px)', 'porto-functionality' ),
					'param_name' => 'border_width',
					'selectors'  => array(
						'{{WRAPPER}} .nav-pills > li > a, {{WRAPPER}} .nav-pills > li > span' => 'border-bottom: {{VALUE}}px solid;',
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Skin Color', 'porto-functionality' ),
					'param_name' => 'skin',
					'std'        => 'custom',
					'value'      => porto_sh_commons( 'colors' ),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Link Padding', 'porto-functionality' ),
					'param_name' => 'link_padding',
					'responsive' => true,
					'selectors'  => array(
						'{{WRAPPER}} .nav-pills > li > a, {{WRAPPER}} .nav-pills > li > span' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Link Typography', 'porto-functionality' ),
					'param_name' => 'link_typography',
					'selectors'  => array(
						'{{WRAPPER}} .nav-pills > li > a, {{WRAPPER}} .nav-pills > li > span',
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Link Color', 'porto-functionality' ),
					'param_name' => 'link_color',
					'selectors'  => array(
						'{{WRAPPER}} .nav-pills > li > a, {{WRAPPER}} .nav-pills > li > span' => 'color: {{VALUE}};',
					),
					'dependency' => array(
						'element' => 'skin',
						'value'   => array( 'custom' ),
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Link Background Color', 'porto-functionality' ),
					'param_name' => 'link_bg_color',
					'selectors'  => array(
						'{{WRAPPER}} .nav-pills > li > a, {{WRAPPER}} .nav-pills > li > span' => 'background-color: {{VALUE}};',
					),
					'dependency' => array(
						'element' => 'skin',
						'value'   => array( 'custom' ),
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Link Border Color', 'porto-functionality' ),
					'param_name' => 'link_br_color',
					'selectors'  => array(
						'{{WRAPPER}} .nav-pills.nav > li > a, {{WRAPPER}} .nav-pills.nav > li > span' => 'border-bottom-color: {{VALUE}};',
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Link Active Color', 'porto-functionality' ),
					'param_name' => 'link_acolor',
					'selectors'  => array(
						'{{WRAPPER}} .nav-pills > li.active > a, {{WRAPPER}} .nav-pills > li:hover > a' => 'color: {{VALUE}};',
					),
					'dependency' => array(
						'element' => 'skin',
						'value'   => array( 'custom' ),
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Link Active Background Color', 'porto-functionality' ),
					'param_name' => 'link_abg_color',
					'selectors'  => array(
						'{{WRAPPER}} .nav-pills > li.active > a, {{WRAPPER}} .nav-pills > li:hover > a' => 'background-color: {{VALUE}};',
					),
					'dependency' => array(
						'element' => 'skin',
						'value'   => array( 'custom' ),
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Link Active Border Color', 'porto-functionality' ),
					'param_name' => 'link_abr_color',
					'selectors'  => array(
						'{{WRAPPER}} .nav-pills.nav > li.active > a, {{WRAPPER}} .nav-pills.nav > li:hover > a' => 'border-bottom-color: {{VALUE}};',
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Sticky_Nav' ) ) {
		class WPBakeryShortCode_Porto_Sticky_Nav extends WPBakeryShortCodesContainer {
		}
	}
}
