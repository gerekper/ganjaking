<?php
// Porto Testimonial
add_action( 'vc_after_init', 'porto_load_testimonial_shortcode' );

function porto_load_testimonial_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();
	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Testimonial', 'porto-functionality' ),
			'base'        => 'porto_testimonial',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Add testimonials to your site to share what customers are saying about services', 'porto-functionality' ),
			'icon'        => 'far fa-comments',
			'params'      => array(
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'heading_name',
					'text'       => esc_html__( 'Name', 'porto-functionality' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Name', 'porto-functionality' ),
					'description' => __( 'Please input the testimonial name.', 'porto-functionality' ),
					'param_name'  => 'name',
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Name Color', 'porto-functionality' ),
					'param_name' => 'name_color',
					'dependency' => array(
						'element'   => 'name',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Typography', 'porto-functionality' ),
					'param_name' => 'name_typography',
					'dependency' => array(
						'element'   => 'name',
						'not_empty' => true,
					),
					'selectors'  => array(
						'{{WRAPPER}} .testimonial-author strong',
					),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Margin', 'porto-functionality' ),
					'param_name' => 'name_margin',
					'dependency' => array(
						'element'   => 'name',
						'not_empty' => true,
					),
					'selectors'  => array(
						'{{WRAPPER}} .testimonial-author strong' => 'margin-top: {{TOP}};margin-right: {{RIGHT}};margin-bottom: {{BOTTOM}};margin-left: {{LEFT}};',
					),
				),
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'heading_role',
					'text'       => esc_html__( 'Role & Company', 'porto-functionality' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Role', 'porto-functionality' ),
					'description' => __( 'Please input the role.', 'porto-functionality' ),
					'param_name'  => 'role',
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Company', 'porto-functionality' ),
					'description' => __( 'Please input the company.', 'porto-functionality' ),
					'param_name'  => 'company',
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Role & Company Color', 'porto-functionality' ),
					'param_name' => 'role_company_color',
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Typography', 'porto-functionality' ),
					'param_name' => 'role_company_typography',
					'selectors'  => array(
						'{{WRAPPER}} .testimonial-author span',
					),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Margin', 'porto-functionality' ),
					'param_name' => 'role_company_margin',
					'selectors'  => array(
						'{{WRAPPER}} .testimonial-author span' => 'margin-top: {{TOP}};margin-right: {{RIGHT}};margin-bottom: {{BOTTOM}};margin-left: {{LEFT}};',
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Author Link', 'porto-functionality' ),
					'param_name' => 'author_url',
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Author Margin', 'porto-functionality' ),
					'param_name' => 'author_margin',
					'selectors'  => array(
						'{{WRAPPER}} .testimonial .testimonial-author' => 'margin-top: {{TOP}};margin-right: {{RIGHT}};margin-bottom: {{BOTTOM}};margin-left: {{LEFT}};',
					),
				),
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'heading_photo',
					'text'       => esc_html__( 'Input Photo URL or Select Photo.', 'porto-functionality' ),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Photo URL', 'porto-functionality' ),
					'param_name' => 'photo_url',
				),
				array(
					'type'       => 'attach_image',
					'heading'    => __( 'Photo', 'porto-functionality' ),
					'param_name' => 'photo_id',
					'dependency' => array(
						'element'  => 'photo_url',
						'is_empty' => true,
					),
				),
				array(
					'type'        => 'porto_number',
					'heading'     => __( 'Photo Max Width', 'porto-functionality' ),
					'param_name'  => 'img_mw',
					'units'       => array( 'px', 'em', 'rem' ),
					'selectors'   => array(
						'{{WRAPPER}} .testimonial-author img' => 'max-width: {{VALUE}}{{UNIT}};',
						'{{WRAPPER}} .testimonial-with-quotes img' => 'width: {{VALUE}}{{UNIT}};',
					),
					'qa_selector' => 'img',
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Margin', 'porto-functionality' ),
					'param_name' => 'img_margin',
					'selectors'  => array(
						'{{WRAPPER}} .testimonial-author img, {{WRAPPER}} .testimonial-with-quotes img' => 'margin-top: {{TOP}};margin-right: {{RIGHT}};margin-bottom: {{BOTTOM}};margin-left: {{LEFT}};',
					),
				),
				array(
					'type'        => 'textarea_html',
					'heading'     => __( 'Content', 'porto-functionality' ),
					'param_name'  => 'content',
					'admin_label' => true,
					'group'       => __( 'Quote', 'porto-functionality' ),
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Content Color', 'porto-functionality' ),
					'param_name'  => 'quote_color',
					'group'       => __( 'Quote', 'porto-functionality' ),
					'qa_selector' => 'blockquote p',
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Typography', 'porto-functionality' ),
					'param_name' => 'content_typography',
					'selectors'  => array(
						'{{WRAPPER}} .testimonial blockquote p',
					),
					'group'      => __( 'Quote', 'porto-functionality' ),
				),
				array(
					'type'        => 'porto_dimension',
					'heading'     => __( 'Padding', 'porto-functionality' ),
					'param_name'  => 'content_padding',
					'selectors'   => array(
						'{{WRAPPER}} .testimonial blockquote' => 'padding-top: {{TOP}};padding-right: {{RIGHT}};padding-bottom: {{BOTTOM}};padding-left: {{LEFT}};',
					),
					'qa_selector' => '.testimonial blockquote',
					'group'       => __( 'Quote', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Quote Color', 'porto-functionality' ),
					'param_name' => 'd_quote_color',
					'group'      => __( 'Quote', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}} .testimonial blockquote:before, {{WRAPPER}} .testimonial blockquote:after' => 'color: {{VALUE}};',
					),
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Typography', 'porto-functionality' ),
					'param_name' => 'quote_typography',
					'selectors'  => array(
						'{{WRAPPER}} .testimonial blockquote:before, {{WRAPPER}} .testimonial blockquote:after',
					),
					'group'      => __( 'Quote', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Position', 'porto-functionality' ),
					'param_name' => 'quote_pos',
					'selectors'  => array(
						'{{WRAPPER}} .testimonial blockquote:before' => 'top: {{TOP}};right: {{RIGHT}};bottom: {{BOTTOM}};left: {{LEFT}};',
					),
					'group'      => __( 'Quote', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Right Quote Position', 'porto-functionality' ),
					'param_name' => 'right_quote_pos',
					'selectors'  => array(
						'{{WRAPPER}} .testimonial blockquote:after' => 'top: {{TOP}};right: {{RIGHT}};bottom: {{BOTTOM}};left: {{LEFT}};',
					),
					'dependency' => array(
						'element' => 'view',
						'value'   => 'transparent',
					),
					'group'      => __( 'Quote', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'heading_type',
					'text'       => esc_html__( 'Type', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'View Type', 'porto-functionality' ),
					'param_name' => 'view',
					'value'      => array(
						__( 'Default', 'porto-functionality' ) => 'default',
						__( 'Default - Author on top', 'porto-functionality' ) => 'default2',
						__( 'Simple', 'porto-functionality' ) => 'simple',
						__( 'Advance', 'porto-functionality' ) => 'advance',
						__( 'With Quotes', 'porto-functionality' ) => 'transparent',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Style Type', 'porto-functionality' ),
					'param_name' => 'style',
					'std'        => '',
					'value'      => porto_sh_commons( 'testimonial_styles' ),
					'dependency' => array(
						'element' => 'view',
						'value'   => array( 'default', 'default2', 'transparent' ),
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Remove Border', 'porto-functionality' ),
					'param_name' => 'remove_border',
					'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Remove Background', 'porto-functionality' ),
					'description' => __( 'Turn on to remove quote background.', 'porto-functionality' ),
					'param_name'  => 'remove_bg',
					'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'dependency'  => array(
						'element' => 'view',
						'value'   => array( 'default', 'default2' ),
					),
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Show with Alternative Font', 'porto-functionality' ),
					'description' => __( 'Turn on to show quote with alternative font.', 'porto-functionality' ),
					'param_name'  => 'alt_font',
					'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'dependency'  => array(
						'element' => 'view',
						'value'   => array( 'default', 'default2' ),
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Skin Color', 'porto-functionality' ),
					'param_name' => 'skin',
					'std'        => 'custom',
					'value'      => porto_sh_commons( 'colors' ),
					'dependency' => array(
						'element' => 'style',
						'value'   => array( '' ),
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Skin Color', 'porto-functionality' ),
					'param_name' => 'color',
					'value'      => array(
						__( 'Normal', 'porto-functionality' ) => '',
						__( 'White', 'porto-functionality' )  => 'white',
					),
					'dependency' => array(
						'element' => 'view',
						'value'   => array( 'transparent', 'simple' ),
					),
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);
	if ( ! class_exists( 'WPBakeryShortCode_Porto_Testimonial' ) ) {
		class WPBakeryShortCode_Porto_Testimonial extends WPBakeryShortCode {
		}
	}
}
