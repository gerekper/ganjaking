<?php
// Porto Stat Counter
add_action( 'vc_after_init', 'porto_load_stat_counter_shortcode' );

function porto_load_stat_counter_shortcode() {

	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => __( 'Porto Counter', 'porto-functionality' ),
			'base'        => 'porto_stat_counter',
			'class'       => 'porto_stat_counter',
			'icon'        => 'fas fa-sort-numeric-down',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Your milestones, achievements, etc.', 'porto-functionality' ),
			'params'      => array(
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Icon to display:', 'porto-functionality' ),
					'param_name'  => 'icon_type',
					'value'       => array(
						__( 'Font Awesome', 'porto-functionality' ) => 'fontawesome',
						__( 'Simple Line Icon', 'porto-functionality' ) => 'simpleline',
						__( 'Porto Icon', 'porto-functionality' ) => 'porto',
						__( 'Custom Image Icon', 'porto-functionality' ) => 'custom',
					),
					'description' => __( 'Use an existing font icon or upload a custom image.', 'porto-functionality' ),
				),
				array(
					'type'       => 'iconpicker',
					'heading'    => __( 'Icon ', 'porto-functionality' ),
					'param_name' => 'icon',
					'value'      => '',
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => 'fontawesome',
					),
				),
				array(
					'type'       => 'iconpicker',
					'heading'    => __( 'Icon', 'porto-functionality' ),
					'param_name' => 'icon_simpleline',
					'settings'   => array(
						'type'         => 'simpleline',
						'iconsPerPage' => 4000,
					),
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => 'simpleline',
					),
				),
				array(
					'type'       => 'iconpicker',
					'heading'    => __( 'Icon', 'porto-functionality' ),
					'param_name' => 'icon_porto',
					'settings'   => array(
						'type'         => 'porto',
						'iconsPerPage' => 4000,
					),
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => 'porto',
					),
				),
				array(
					'type'        => 'attach_image',
					'heading'     => __( 'Upload Image Icon:', 'porto-functionality' ),
					'param_name'  => 'icon_img',
					'value'       => '',
					'description' => __( 'Upload the custom image icon.', 'porto-functionality' ),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Icon Position', 'porto-functionality' ),
					'param_name'  => 'icon_position',
					'value'       => array(
						__( 'Top', 'porto-functionality' ) => 'top',
						__( 'Right', 'porto-functionality' ) => 'right',
						__( 'Left', 'porto-functionality' ) => 'left',
					),
					'description' => __( 'Enter Position of Icon', 'porto-functionality' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Counter Title', 'porto-functionality' ),
					'param_name'  => 'counter_title',
					'admin_label' => true,
					'value'       => '',
					'description' => __( 'Enter title for stats counter block', 'porto-functionality' ),
				),
				array(
					'type'        => 'textfield',
					'class'       => '',
					'heading'     => __( 'Counter Value', 'porto-functionality' ),
					'param_name'  => 'counter_value',
					'value'       => '1250',
					'description' => __( 'Enter number for counter without any special character. You may enter a decimal number. Eg 12.76', 'porto-functionality' ),
				),
				array(
					'type'        => 'textfield',
					'class'       => '',
					'heading'     => __( 'Thousands Separator', 'porto-functionality' ),
					'param_name'  => 'counter_sep',
					'value'       => ',',
					'description' => __( "Enter character for thousanda separator. e.g. ',' will separate 125000 into 125,000", 'porto-functionality' ),
				),
				array(
					'type'        => 'textfield',
					'class'       => '',
					'heading'     => __( 'Replace Decimal Point With', 'porto-functionality' ),
					'param_name'  => 'counter_decimal',
					'value'       => '.',
					'description' => __( "Did you enter a decimal number (Eg - 12.76) The decimal point '.' will be replaced with value that you will enter above.", 'porto-functionality' ),
				),
				array(
					'type'        => 'textfield',
					'class'       => '',
					'heading'     => __( 'Counter Value Prefix', 'porto-functionality' ),
					'param_name'  => 'counter_prefix',
					'value'       => '',
					'description' => __( 'Enter prefix for counter value', 'porto-functionality' ),
				),
				array(
					'type'        => 'textfield',
					'class'       => '',
					'heading'     => __( 'Counter Value Suffix', 'porto-functionality' ),
					'param_name'  => 'counter_suffix',
					'value'       => '',
					'description' => __( 'Enter suffix for counter value', 'porto-functionality' ),
				),
				array(
					'type'        => 'number',
					'heading'     => __( 'Counter rolling time', 'porto-functionality' ),
					'param_name'  => 'speed',
					'value'       => 3,
					'min'         => 1,
					'max'         => 10,
					'suffix'      => 'seconds',
					'description' => __( 'How many seconds the counter should roll?', 'porto-functionality' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Extra Class', 'porto-functionality' ),
					'param_name'  => 'el_class',
					'value'       => '',
					'description' => __( 'Add extra class name that will be applied to the icon process, and you can use this class for your customizations.', 'porto-functionality' ),
				),

				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Icon Style', 'porto-functionality' ),
					'description' => __( 'Circle Image is worked for only image icon.', 'porto-functionality' ),
					'param_name'  => 'icon_style',
					'value'       => array(
						__( 'Simple', 'porto-functionality' ) => 'none',
						__( 'Circle Background', 'porto-functionality' ) => 'circle',
						__( 'Circle Image', 'porto-functionality' ) => 'circle_img',
						__( 'Square Background', 'porto-functionality' ) => 'square',
						__( 'Advanced', 'porto-functionality' ) => 'advanced',
					),
					'group'       => esc_html__( 'Icon Style', 'porto-functionality' ),
					'qa_selector' => '.porto-icon',
				),
				array(
					'type'        => 'number',
					'heading'     => __( 'Image Width', 'porto-functionality' ),
					'param_name'  => 'img_width',
					'value'       => 48,
					'min'         => 16,
					'max'         => 512,
					'suffix'      => 'px',
					'description' => __( 'Provide image width', 'porto-functionality' ),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => array( 'custom' ),
					),
					'group'       => esc_html__( 'Icon Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'number',
					'heading'    => __( 'Icon Size', 'porto-functionality' ),
					'param_name' => 'icon_size',
					'value'      => 32,
					'min'        => 12,
					'max'        => 72,
					'suffix'     => 'px',
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => array( 'fontawesome', 'simpleline', 'porto' ),
					),
					'group'      => esc_html__( 'Icon Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'param_name' => 'icon_color',
					'value'      => '#333333',
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => array( 'fontawesome', 'simpleline', 'porto' ),
					),
					'group'      => esc_html__( 'Icon Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Background Color', 'porto-functionality' ),
					'param_name'  => 'icon_color_bg',
					'value'       => '#ffffff',
					'description' => __( 'Select background color for icon.', 'porto-functionality' ),
					'dependency'  => array(
						'element' => 'icon_style',
						'value'   => array( 'circle', 'circle_img', 'square', 'advanced' ),
					),
					'selectors'   => array(
						'{{WRAPPER}} .porto-sicon-img.porto-u-circle-img:before' => 'border-color: {{VALUE}};',
						//'{{WRAPPER}} .porto-sicon-img' => 'background: {{VALUE}};',
						//'{{WRAPPER}} .porto-icon'      => 'background: {{VALUE}};',
					),
					'group'       => esc_html__( 'Icon Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Icon Border Style', 'porto-functionality' ),
					'param_name'  => 'icon_border_style',
					'value'       => array(
						__( 'None', 'porto-functionality' )   => '',
						__( 'Solid', 'porto-functionality' )  => 'solid',
						__( 'Dashed', 'porto-functionality' ) => 'dashed',
						__( 'Dotted', 'porto-functionality' ) => 'dotted',
						__( 'Double', 'porto-functionality' ) => 'double',
						__( 'Inset', 'porto-functionality' )  => 'inset',
						__( 'Outset', 'porto-functionality' ) => 'outset',
					),
					'description' => __( 'Select the border style for icon.', 'porto-functionality' ),
					'dependency'  => array(
						'element' => 'icon_style',
						'value'   => array( 'advanced', 'circle_img' ),
					),
					'group'       => esc_html__( 'Icon Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Border Color', 'porto-functionality' ),
					'param_name'  => 'icon_color_border',
					'value'       => '#333333',
					'description' => __( 'Select border color for icon.', 'porto-functionality' ),
					'dependency'  => array(
						'element'   => 'icon_border_style',
						'not_empty' => true,
					),
					'group'       => esc_html__( 'Icon Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'number',
					'heading'     => __( 'Border Width', 'porto-functionality' ),
					'param_name'  => 'icon_border_size',
					'value'       => 1,
					'min'         => 1,
					'max'         => 10,
					'suffix'      => 'px',
					'description' => __( 'Thickness of the border.', 'porto-functionality' ),
					'dependency'  => array(
						'element'   => 'icon_border_style',
						'not_empty' => true,
					),
					'group'       => esc_html__( 'Icon Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'number',
					'heading'    => __( 'Border Radius', 'porto-functionality' ),
					'param_name' => 'icon_border_radius',
					'value'      => 500,
					'min'        => 1,
					'max'        => 500,
					'suffix'     => 'px',
					'dependency' => array(
						'element'   => 'icon_border_style',
						'not_empty' => true,
					),
					'group'      => esc_html__( 'Icon Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'number',
					'heading'     => __( 'Size (px)', 'porto-functionality' ),
					'param_name'  => 'icon_border_spacing',
					'value'       => 50,
					'min'         => 0,
					'max'         => 500,
					'suffix'      => 'px',
					'description' => __( 'Icon width and height for font icon and Spacing from center of the icon till the boundary of border / background for image icon', 'porto-functionality' ),
					'dependency'  => array(
						'element' => 'icon_style',
						'value'   => array( 'circle_img', 'advanced' ),
					),
					'selectors'   => array(
						'{{WRAPPER}} .porto-icon.advanced' => 'width: {{VALUE}}px; height: {{VALUE}}px; line-height: {{VALUE}}px;',
						'{{WRAPPER}} .porto-sicon-img.porto-u-circle-img:before' => 'border-width: calc({{VALUE}}px + 1px);',
						'{{WRAPPER}} .porto-sicon-img'     => 'padding: {{VALUE}}px;',
					),
					'group'       => esc_html__( 'Icon Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Icon Margin', 'porto-functionality' ),
					'param_name' => 'icon_margin',
					'selectors'  => array(
						'{{WRAPPER}} .porto-icon, {{WRAPPER}} .porto-sicon-img' => 'margin-top: {{TOP}};margin-right: {{RIGHT}};margin-bottom: {{BOTTOM}};margin-left: {{LEFT}};',
					),
					'group'      => esc_html__( 'Icon Style', 'porto-functionality' ),
				),

				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'title_text_typography',
					'text'       => __( 'Counter Title settings', 'porto-functionality' ),
					'group'      => esc_html__( 'Text Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'porto_typography',
					'heading'     => __( 'Typography', 'porto-functionality' ),
					'param_name'  => 'title_font_porto_typography',
					'group'       => esc_html__( 'Text Style', 'porto-functionality' ),
					'selectors'   => array(
						'{{WRAPPER}} .stats-text',
					),
					'qa_selector' => '.stats-text',
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Color', 'porto-functionality' ),
					'param_name'  => 'counter_color_txt',
					'value'       => '',
					'description' => __( 'Select text color for counter title.', 'porto-functionality' ),
					'group'       => esc_html__( 'Text Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'desc_text_typography',
					'text'       => __( 'Counter Value settings', 'porto-functionality' ),
					'group'      => esc_html__( 'Text Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Typography', 'porto-functionality' ),
					'param_name' => 'desc_font_porto_typography',
					'group'      => esc_html__( 'Text Style', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}} .stats-number',
					),
				),
				array(
					'type'        => 'colorpicker',
					'param_name'  => 'desc_font_color',
					'heading'     => __( 'Color', 'porto-functionality' ),
					'description' => __( 'Select text color for counter digits.', 'porto-functionality' ),
					'group'       => esc_html__( 'Text Style', 'porto-functionality' ),
					'qa_selector' => '.stats-number',
				),
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'suf_pref_typography',
					'text'       => __( 'Counter suffix-prefix Value settings', 'porto-functionality' ),
					'group'      => esc_html__( 'Text Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Typography', 'porto-functionality' ),
					'param_name' => 'suf_pref_font_porto_typography',
					'group'      => esc_html__( 'Text Style', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}}.stats-block .counter_prefix, {{WRAPPER}}.stats-block .counter_suffix',
					),
				),
				array(
					'type'        => 'colorpicker',
					'param_name'  => 'suf_pref_font_color',
					'heading'     => __( 'Color', 'porto-functionality' ),
					'description' => __( 'Select text color for counter prefix and suffix.', 'porto-functionality' ),
					'group'       => esc_html__( 'Text Style', 'porto-functionality' ),
					'qa_selector' => '.counter_prefix, .counter_suffix',
				),
				array(
					'type'       => 'porto_number',
					'heading'    => esc_html__( 'Spacing between Title & Counter Value', 'porto-functionality' ),
					'param_name' => 'spacing',
					'units'      => array( 'px', 'rem' ),
					'selectors'  => array(
						'{{WRAPPER}} .stats-text' => 'margin-top: {{VALUE}}{{UNIT}};',
					),
					'group'      => esc_html__( 'Text Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'porto_number',
					'heading'     => esc_html__( 'Margin Bottom', 'porto-functionality' ),
					'description' => esc_html__( 'Default is 35px.', 'porto-functionality' ),
					'param_name'  => 'wrap_margin_bottom',
					'units'       => array( 'px', 'rem' ),
					'selectors'   => array(
						'{{WRAPPER}}' => 'margin-bottom: {{VALUE}}{{UNIT}};',
					),
					'group'       => esc_html__( 'Text Style', 'porto-functionality' ),
				),
				array(
					'type'             => 'css_editor',
					'heading'          => __( 'Css', 'porto-functionality' ),
					'param_name'       => 'css_stat_counter',
					'group'            => __( 'Design ', 'porto-functionality' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column no-vc-background no-vc-border creative_link_css_editor',
				),

				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( class_exists( 'WPBakeryShortCode' ) ) {
		class WPBakeryShortCode_porto_stat_counter extends WPBakeryShortCode {
		}
	}
}
