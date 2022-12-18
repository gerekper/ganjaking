<?php
// Porto Ultimate Heading
add_action( 'vc_after_init', 'porto_load_ultimate_heading_shortcode' );

function porto_load_ultimate_heading_shortcode() {

	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => __( 'Porto Headings', 'porto-functionality' ),
			'base'        => 'porto_ultimate_heading',
			'class'       => 'porto_ultimate_heading',
			'icon'        => 'fas fa-text-height',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Awesome heading styles.', 'porto-functionality' ),
			'params'      => array_merge(
				Porto_Wpb_Dynamic_Tags::get_instance()->dynamic_wpb_tags( 'field' ),
				array(
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Title', 'porto-functionality' ),
						'param_name' => 'main_heading',
						'holder'     => 'div',
						'value'      => '',
						'dependency' => array(
							'element'  => 'enable_field_dynamic',
							'is_empty' => true,
						),
					),
					array(
						'type'        => 'checkbox',
						'heading'     => __( 'Enable Typewriter Effect', 'porto-functionality' ),
						'param_name'  => 'enable_typewriter',
						'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
						'qa_selector' => '.porto-u-main-heading',
					),
					array(
						'type'        => 'checkbox',
						'heading'     => __( 'Effect By Words', 'porto-functionality' ),
						'description' => __( 'Animate the words one by one.', 'porto-functionality' ),
						'param_name'  => 'enable_typeword',
						'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
						'dependency'  => array(
							'element'   => 'enable_typewriter',
							'not_empty' => true,
						),
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Animation Name e.g: typeWriter, fadeIn and so on.', 'porto-functionality' ),
						'param_name' => 'typewriter_animation',
						'value'      => 'fadeIn',
						'dependency' => array(
							'element'   => 'enable_typewriter',
							'not_empty' => true,
						),
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Start Delay(ms)', 'porto-functionality' ),
						'param_name' => 'typewriter_delay',
						'value'      => '',
						'dependency' => array(
							'element'   => 'enable_typewriter',
							'not_empty' => true,
						),
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Animation Speed(ms)', 'porto-functionality' ),
						'param_name' => 'typewriter_speed',
						'std'        => '50',
						'dependency' => array(
							'element'   => 'enable_typewriter',
							'not_empty' => true,
						),
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Please input min width that can work. (px)', 'porto-functionality' ),
						'param_name' => 'typewriter_width',
						'value'      => '',
						'dependency' => array(
							'element'   => 'enable_typewriter',
							'not_empty' => true,
						),
					),
					array(
						'type'       => 'porto_param_heading',
						'text'       => __( 'Heading Settings', 'porto-functionality' ),
						'param_name' => 'main_heading_typograpy',
						'group'      => 'Typography',
						'class'      => '',
					),
					array(
						'type'       => 'porto_typography',
						'heading'    => __( 'Typography', 'porto-functionality' ),
						'param_name' => 'main_heading_porto_typography',
						'group'      => 'Typography',
						'selectors'  => array(
							'{{WRAPPER}}.porto-u-heading .porto-u-main-heading > *',
						),
					),
					array(
						'type'       => 'colorpicker',
						'class'      => '',
						'heading'    => __( 'Font Color', 'porto-functionality' ),
						'param_name' => 'main_heading_color',
						'value'      => '',
						'group'      => 'Typography',
					),
					array(
						'type'       => 'number',
						'heading'    => __( 'Heading Margin Bottom', 'porto-functionality' ),
						'param_name' => 'main_heading_margin_bottom',
						'suffix'     => 'px',
						'group'      => 'Design',
					),
					array(
						'type'             => 'textarea_html',
						'edit_field_class' => 'vc_col-xs-12 vc_column wpb_el_type_textarea_html vc_wrapper-param-type-textarea_html vc_shortcode-param',
						'heading'          => __( 'Sub Heading (Optional)', 'porto-functionality' ),
						'param_name'       => 'content',
						'value'            => '',
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Tag', 'porto-functionality' ),
						'param_name'  => 'heading_tag',
						'value'       => array(
							__( 'Default', 'porto-functionality' ) => 'h2',
							__( 'H1', 'porto-functionality' )  => 'h1',
							__( 'H3', 'porto-functionality' )  => 'h3',
							__( 'H4', 'porto-functionality' )  => 'h4',
							__( 'H5', 'porto-functionality' )  => 'h5',
							__( 'H6', 'porto-functionality' )  => 'h6',
							__( 'div', 'porto-functionality' ) => 'div',
						),
						'description' => __( 'Default is H2.', 'porto-functionality' ),
					),
					array(
						'type'             => 'porto_param_heading',
						'text'             => __( 'Sub Heading Settings', 'porto-functionality' ),
						'param_name'       => 'sub_heading_typograpy',
						'group'            => 'Typography',
						'class'            => '',
						'edit_field_class' => 'vc_column vc_col-sm-12',
						'qa_selector'      => '.porto-u-sub-heading',
					),
					array(
						'type'       => 'porto_typography',
						'heading'    => __( 'Typography', 'porto-functionality' ),
						'param_name' => 'sub_heading_porto_typography',
						'group'      => 'Typography',
						'selectors'  => array(
							'{{WRAPPER}} .porto-u-sub-heading',
						),
					),
					array(
						'type'       => 'colorpicker',
						'class'      => '',
						'heading'    => __( 'Font Color', 'porto-functionality' ),
						'param_name' => 'sub_heading_color',
						'value'      => '',
						'group'      => 'Typography',
					),
					array(
						'type'       => 'number',
						'heading'    => 'Sub Heading Margin Bottom',
						'param_name' => 'sub_heading_margin_bottom',
						'suffix'     => 'px',
						'group'      => 'Design',
					),
					array(
						'type'       => 'porto_button_group',
						'heading'    => __( 'Alignment', 'porto-functionality' ),
						'param_name' => 'alignment',
						'value'      => array(
							'left'   => array(
								'title' => esc_html__( 'Left', 'porto-functionality' ),
								'icon'  => 'fas fa-align-left',
							),
							'center' => array(
								'title' => esc_html__( 'Center', 'porto-functionality' ),
								'icon'  => 'fas fa-align-center',
							),
							'right'  => array(
								'title' => esc_html__( 'Right', 'porto-functionality' ),
								'icon'  => 'fas fa-align-right',
							),
						),
						'responsive' => true,
						'std'        => 'center',
					),
					array(
						'type'        => 'dropdown',
						'class'       => '',
						'heading'     => __( 'Seperator', 'porto-functionality' ),
						'param_name'  => 'spacer',
						'value'       => array(
							__( 'No Seperator', 'porto-functionality' ) => 'no_spacer',
							__( 'Line', 'porto-functionality' )  => 'line_only',
						),
						'description' => __( 'Horizontal line to divide sections.', 'porto-functionality' ),
					),
					array(
						'type'       => 'dropdown',
						'class'      => '',
						'heading'    => __( 'Seperator Position', 'porto-functionality' ),
						'param_name' => 'spacer_position',
						'value'      => array(
							__( 'Top', 'porto-functionality' ) => 'top',
							__( 'Between Heading & Sub-Heading', 'porto-functionality' ) => 'middle',
							__( 'Bottom', 'porto-functionality' ) => 'bottom',
						),
						'dependency' => array(
							'element' => 'spacer',
							'value'   => array( 'line_only' ),
						),
					),
					array(
						'type'       => 'number',
						'class'      => '',
						'heading'    => __( 'Line Width (optional)', 'porto-functionality' ),
						'param_name' => 'line_width',
						'suffix'     => 'px',
						'dependency' => array(
							'element' => 'spacer',
							'value'   => array( 'line_only' ),
						),
					),
					array(
						'type'       => 'number',
						'class'      => '',
						'heading'    => __( 'Line Height', 'porto-functionality' ),
						'param_name' => 'line_height',
						'value'      => 1,
						'min'        => 1,
						'max'        => 500,
						'suffix'     => 'px',
						'dependency' => array(
							'element' => 'spacer',
							'value'   => array( 'line_only' ),
						),
					),
					array(
						'type'       => 'colorpicker',
						'class'      => '',
						'heading'    => __( 'Line Color', 'porto-functionality' ),
						'param_name' => 'line_color',
						'value'      => '#333333',
						'dependency' => array(
							'element' => 'spacer',
							'value'   => array( 'line_only' ),
						),
					),
					array(
						'type'       => 'number',
						'heading'    => __( 'Seperator Margin Bottom', 'porto-functionality' ),
						'param_name' => 'spacer_margin_bottom',
						'suffix'     => 'px',
						'dependency' => array(
							'element' => 'spacer',
							'value'   => array( 'line_only' ),
						),
						'group'      => 'Design',
					),
					$animation_type,
					$animation_duration,
					$animation_delay,
					array(
						'type'       => 'attach_image',
						'heading'    => __( 'Floating Image', 'porto-functionality' ),
						'param_name' => 'floating_img',
						'group'      => __( 'Animation', 'porto-functionality' ),
					),
					array(
						'type'        => 'number',
						'heading'     => __( 'Floating Offset', 'porto-functionality' ),
						'param_name'  => 'floating_offset',
						'description' => __( 'Control the offset from the cursor.', 'porto-functionality' ),
						'dependency'  => array(
							'element'   => 'floating_img',
							'not_empty' => true,
						),
						'group'       => __( 'Animation', 'porto-functionality' ),
					),
					array(
						'type'       => 'porto_param_heading',
						'param_name' => 'desc_highlight',
						'text'       => esc_html__( 'For highlight, the main heading should have the HTML Mark Text element. For example A<mark>B</mark>C.', 'porto-functionality' ),
						'group'      => __( 'Highlight', 'porto-functionality' ),
					),
					array(
						'type'       => 'checkbox',
						'heading'    => __( 'Enable Highlight Animation', 'porto-functionality' ),
						'param_name' => 'enable_highlight',
						'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
						'group'      => __( 'Highlight', 'porto-functionality' ),
					),
					array(
						'type'        => 'checkbox',
						'heading'     => __( 'Gradient Background', 'porto-functionality' ),
						'param_name'  => 'hlight_gradient',
						'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
						'description' => __( 'Set the gradient Background for highlight.', 'porto-functionality' ),
						'dependency'  => array(
							'element'   => 'enable_highlight',
							'not_empty' => true,
						),
						'group'       => __( 'Highlight', 'porto-functionality' ),
					),
					array(
						'type'        => 'colorpicker',
						'heading'     => __( 'Background Color', 'porto-functionality' ),
						'param_name'  => 'hlight_bg',
						'selectors'   => array(
							'{{WRAPPER}} .heading-highlight mark:before' => 'background-color: {{VALUE}};',
						),
						'description' => __( 'Control the highlight background.', 'porto-functionality' ),
						'dependency'  => array(
							'element'  => 'hlight_gradient',
							'is_empty' => true,
						),
						'group'       => __( 'Highlight', 'porto-functionality' ),
					),
					array(
						'type'        => 'colorpicker',
						'heading'     => __( 'Top Color', 'porto-functionality' ),
						'param_name'  => 'hlight_top_bg',
						'dependency'  => array(
							'element'   => 'hlight_gradient',
							'not_empty' => true,
						),
						'description' => __( 'Control the first color for gradient.', 'porto-functionality' ),
						'group'       => __( 'Highlight', 'porto-functionality' ),
					),
					array(
						'type'        => 'number',
						'heading'     => __( 'Top Location (%)', 'porto-functionality' ),
						'param_name'  => 'hlight_top_loc',
						'dependency'  => array(
							'element'   => 'hlight_gradient',
							'not_empty' => true,
						),
						'description' => __( 'Control the location of starting point.', 'porto-functionality' ),
						'group'       => __( 'Highlight', 'porto-functionality' ),
					),
					array(
						'type'        => 'colorpicker',
						'heading'     => __( 'Bottom Color', 'porto-functionality' ),
						'param_name'  => 'hlight_bottom_bg',
						'dependency'  => array(
							'element'   => 'hlight_gradient',
							'not_empty' => true,
						),
						'description' => __( 'Control the last color for the gradient.', 'porto-functionality' ),
						'group'       => __( 'Highlight', 'porto-functionality' ),
					),
					array(
						'type'        => 'number',
						'heading'     => __( 'Bottom Location (%)', 'porto-functionality' ),
						'param_name'  => 'hlight_bottom_loc',
						'dependency'  => array(
							'element'   => 'hlight_gradient',
							'not_empty' => true,
						),
						'description' => __( 'Control the location of ending point.', 'porto-functionality' ),
						'group'       => __( 'Highlight', 'porto-functionality' ),
					),
					array(
						'type'        => 'number',
						'heading'     => __( 'Angle (deg)', 'porto-functionality' ),
						'param_name'  => 'hlight_angle',
						'dependency'  => array(
							'element'   => 'hlight_gradient',
							'not_empty' => true,
						),
						'description' => __( 'Control the angle of gradient direction.', 'porto-functionality' ),
						'group'       => __( 'Highlight', 'porto-functionality' ),
					),
					array(
						'type'       => 'number',
						'heading'    => __( 'Animation Delay(ms)', 'porto-functionality' ),
						'param_name' => 'animation_hlight_delay',
						'dependency' => array(
							'element'   => 'enable_highlight',
							'not_empty' => true,
						),
						'selectors'  => array(
							'{{WRAPPER}} .heading-highlight mark:before' => 'animation-delay: {{VALUE}}ms;',
						),
						'group'      => __( 'Highlight Style', 'porto-functionality' ),
					),
					array(
						'type'        => 'porto_number',
						'heading'     => __( 'Height', 'porto-functionality' ),
						'param_name'  => 'hlight_height',
						'units'       => array( '%' ),
						'dependency'  => array(
							'element'   => 'enable_highlight',
							'not_empty' => true,
						),
						'responsive'  => true,
						'selectors'   => array(
							'{{WRAPPER}} .heading-highlight mark:before' => 'height: {{VALUE}}{{UNIT}}',
						),
						'description' => __( 'Control the height of the highlight.', 'porto-functionality' ),
						'group'       => __( 'Highlight Style', 'porto-functionality' ),
					),
					array(
						'type'        => 'porto_number',
						'heading'     => __( 'Vertical Position', 'porto-functionality' ),
						'param_name'  => 'hlight_bottom',
						'units'       => array( '%' ),
						'dependency'  => array(
							'element'   => 'enable_highlight',
							'not_empty' => true,
						),
						'responsive'  => true,
						'selectors'   => array(
							'{{WRAPPER}} .heading-highlight mark:before' => 'bottom: {{VALUE}}{{UNIT}};',
						),
						'description' => __( 'Control the bottom position of the highlight.', 'porto-functionality' ),
						'group'       => __( 'Highlight Style', 'porto-functionality' ),
					),
					array(
						'type'        => 'porto_number',
						'heading'     => __( 'Horizontal Position', 'porto-functionality' ),
						'param_name'  => 'hlight_left',
						'units'       => array( '%' ),
						'dependency'  => array(
							'element'   => 'enable_highlight',
							'not_empty' => true,
						),
						'responsive'  => true,
						'selectors'   => array(
							'{{WRAPPER}} .heading-highlight mark:before' => 'left: {{VALUE}}{{UNIT}};',
						),
						'description' => __( 'Control the left position of the highlight.', 'porto-functionality' ),
						'group'       => __( 'Highlight Style', 'porto-functionality' ),
					),
					$custom_class,
				),
				porto_shortcode_floating_fields(),
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Ultimate_Headings' ) ) {
		class WPBakeryShortCode_Porto_Ultimate_Headings extends WPBakeryShortCodesContainer {
			protected $controls_list = array(
				'add',
				'edit',
				'clone',
				'delete',
			);
		}
	}
}
