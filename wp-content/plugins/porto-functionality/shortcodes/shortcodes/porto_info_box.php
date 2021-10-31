<?php
// Porto Info Box
add_action( 'vc_after_init', 'porto_load_info_box_shortcode' );

function porto_load_info_box_shortcode() {

	$animation_type = porto_vc_animation_type();
	$custom_class   = porto_vc_custom_class();

	$animation_type1    = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();

	$animation_type1['param_name'] = 'animation_type1';
	$animation_type['group']       = '';

	vc_map(
		array(
			'name'                    => __( 'Porto Info Box', 'porto-functionality' ),
			'base'                    => 'porto_info_box',
			'icon'                    => 'fas fa-archive',
			'class'                   => 'porto_info_box',
			'category'                => __( 'Porto', 'porto-functionality' ),
			'description'             => __( 'Adds icon box with custom font icon', 'porto-functionality' ),
			'controls'                => 'full',
			'show_settings_on_create' => true,
			'params'                  => array(
				array(
					'type'        => 'dropdown',
					'class'       => '',
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
					'class'      => '',
					'heading'    => __( 'Icon ', 'porto-functionality' ),
					'param_name' => 'icon',
					'value'      => '',
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => array( 'fontawesome' ),
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
					'class'       => '',
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
					'type'        => 'number',
					'class'       => '',
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
				),
				array(
					'type'       => 'number',
					'class'      => '',
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
				),
				array(
					'type'       => 'colorpicker',
					'class'      => '',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'param_name' => 'icon_color',
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => array( 'fontawesome', 'simpleline', 'porto' ),
					),
				),
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => __( 'Icon Style', 'porto-functionality' ),
					'param_name' => 'icon_style',
					'value'      => array(
						__( 'Simple', 'porto-functionality' ) => 'none',
						__( 'Circle Background', 'porto-functionality' ) => 'circle',
						__( 'Circle Image', 'porto-functionality' ) => 'circle_img',
						__( 'Square Background', 'porto-functionality' ) => 'square',
						__( 'Design your own', 'porto-functionality' ) => 'advanced',
					),
				),
				array(
					'type'        => 'colorpicker',
					'class'       => '',
					'heading'     => __( 'Background Color', 'porto-functionality' ),
					'param_name'  => 'icon_color_bg',
					'description' => __( 'Select background color for icon.', 'porto-functionality' ),
					'dependency'  => array(
						'element' => 'icon_style',
						'value'   => array( 'circle', 'circle_img', 'square', 'advanced' ),
					),
					'std'         => '',
				),
				array(
					'type'        => 'dropdown',
					'class'       => '',
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
						'value'   => array( 'circle_img', 'advanced' ),
					),
				),
				array(
					'type'        => 'colorpicker',
					'class'       => '',
					'heading'     => __( 'Border Color', 'porto-functionality' ),
					'param_name'  => 'icon_color_border',
					'value'       => '#333333',
					'description' => __( 'Select border color for icon.', 'porto-functionality' ),
					'dependency'  => array(
						'element'   => 'icon_border_style',
						'not_empty' => true,
					),
				),
				array(
					'type'        => 'number',
					'class'       => '',
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
				),
				array(
					'type'       => 'number',
					'class'      => '',
					'heading'    => __( 'Border Radius', 'porto-functionality' ),
					'param_name' => 'icon_border_radius',
					'value'      => 500,
					'min'        => 1,
					'max'        => 500,
					'suffix'     => 'px',
					'dependency'  => array(
						'element' => 'icon_style',
						'value'   => array( 'none', 'circle', 'circle_img', 'advanced' ),
					),
				),
				array(
					'type'        => 'number',
					'class'       => '',
					'heading'     => __( 'Background Size', 'porto-functionality' ),
					'param_name'  => 'icon_border_spacing',
					'value'       => 50,
					'min'         => 0,
					'max'         => 500,
					'suffix'      => 'px',
					'description' => __( 'Spacing from center of the icon till the boundary of border / background', 'porto-functionality' ),
					'dependency'  => array(
						'element' => 'icon_style',
						'value'   => array( 'circle_img', 'advanced' ),
					),
				),
				$animation_type,
				array(
					'type'        => 'textfield',
					'class'       => '',
					'heading'     => __( 'Title', 'porto-functionality' ),
					'param_name'  => 'title',
					'admin_label' => true,
					'value'       => '',
					'description' => __( 'Provide the title for this icon box.', 'porto-functionality' ),
				),
				array(
					'type'        => 'textfield',
					'class'       => '',
					'heading'     => __( 'Sub title', 'porto-functionality' ),
					'param_name'  => 'subtitle',
					'admin_label' => true,
					'value'       => '',
					'description' => __( 'Provide the sub title for this icon box.', 'porto-functionality' ),
					//"dependency" => array("element" => "title", "not_empty" => true),
				),
				array(
					'type'             => 'textarea_html',
					'class'            => '',
					'heading'          => __( 'Description', 'porto-functionality' ),
					'param_name'       => 'content',
					'value'            => '',
					'description'      => __( 'Provide the description for this icon box.', 'porto-functionality' ),
					'edit_field_class' => 'vc_col-xs-12 vc_column wpb_el_type_textarea_html vc_wrapper-param-type-textarea_html vc_shortcode-param',
				),
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => __( 'Apply link to:', 'porto-functionality' ),
					'param_name' => 'read_more',
					'value'      => array(
						__( 'No Link', 'porto-functionality' )   => 'none',
						__( 'Complete Box', 'porto-functionality' ) => 'box',
						__( 'Box Title', 'porto-functionality' ) => 'title',
						__( 'Display Read More', 'porto-functionality' ) => 'more',
					),
				),
				array(
					'type'        => 'vc_link',
					'class'       => '',
					'heading'     => __( 'Add Link', 'porto-functionality' ),
					'param_name'  => 'link',
					'value'       => '',
					'description' => __( 'Add a custom link or select existing page.', 'porto-functionality' ),
					'dependency'  => array(
						'element' => 'read_more',
						'value'   => array( 'box', 'title', 'more' ),
					),
				),
				array(
					'type'        => 'textfield',
					'class'       => '',
					'heading'     => __( 'Read More Text', 'porto-functionality' ),
					'param_name'  => 'read_text',
					'value'       => 'Read More',
					'description' => __( 'Customize the read more text.', 'porto-functionality' ),
					'dependency'  => array(
						'element' => 'read_more',
						'value'   => array( 'more' ),
					),
				),
				array(
					'type'        => 'dropdown',
					'class'       => '',
					'heading'     => __( 'Select Hover Effect type', 'porto-functionality' ),
					'param_name'  => 'hover_effect',
					'value'       => array(
						__( 'No Effect', 'porto-functionality' ) => 'style_1',
						__( 'Icon Zoom', 'porto-functionality' ) => 'style_2',
						__( 'Icon Slide Up', 'porto-functionality' ) => 'style_3',
						__( 'Icon Slide Left', 'porto-functionality' ) => 'hover-icon-left',
						__( 'Icon Slide Right', 'porto-functionality' ) => 'hover-icon-right',
					),
					'description' => __( 'Select the type of effct you want on hover', 'porto-functionality' ),
				),
				array(
					'type'        => 'dropdown',
					'class'       => '',
					'heading'     => __( 'Box Style', 'porto-functionality' ),
					'param_name'  => 'pos',
					'value'       => array(
						__( 'Icon at Left with heading', 'porto-functionality' ) => 'default',
						__( 'Icon at Right with heading', 'porto-functionality' ) => 'heading-right',
						__( 'Icon at Left', 'porto-functionality' ) => 'left',
						__( 'Icon at Right', 'porto-functionality' ) => 'right',
						__( 'Icon at Top', 'porto-functionality' ) => 'top',
					),
					'description' => __( 'Select icon position. Icon box style will be changed according to the icon position.', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_button_group',
					'heading'    => __( 'Horizontal Alignment', 'porto-functionality' ),
					'param_name' => 'h_align',
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
					'std'        => 'center',
					'dependency' => array(
						'element' => 'pos',
						'value'   => array( 'top' ),
					),
				),
				$custom_class,
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'title_text_typography',
					'text'       => __( 'Title settings', 'porto-functionality' ),
					'group'      => 'Style',
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Tag', 'porto-functionality' ),
					'param_name'  => 'heading_tag',
					'value'       => array(
						__( 'Default', 'porto-functionality' ) => 'h3',
						__( 'H1', 'porto-functionality' ) => 'h1',
						__( 'H2', 'porto-functionality' ) => 'h2',
						__( 'H4', 'porto-functionality' ) => 'h4',
						__( 'H5', 'porto-functionality' ) => 'h5',
						__( 'H6', 'porto-functionality' ) => 'h6',
					),
					'description' => __( 'Default is H3', 'porto-functionality' ),
					'group'       => 'Style',
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Typography', 'porto-functionality' ),
					'param_name' => 'title_font_porto_typography',
					'group'      => 'Style',
					'selectors'  => array(
						'{{WRAPPER}} .porto-sicon-title',
					),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'title_font_color',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'group'      => 'Style',
				),
				array(
					'type'             => 'porto_param_heading',
					'param_name'       => 'subtitle_text_typography',
					'heading'          => __( 'Sub title settings', 'porto-functionality' ),
					'value'            => '',
					'group'            => 'Style',
					'edit_field_class' => 'no-top-margin vc_column vc_col-sm-12',
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Typography', 'porto-functionality' ),
					'param_name' => 'subtitle_font_porto_typography',
					'group'      => 'Style',
					'selectors'  => array(
						'{{WRAPPER}} .porto-sicon-header p',
					),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'subtitle_font_color',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'group'      => 'Style',
				),
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'desc_text_typography',
					'text'       => __( 'Description settings', 'porto-functionality' ),
					'group'      => 'Style',
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Typography', 'porto-functionality' ),
					'param_name' => 'desc_font_porto_typography',
					'group'      => 'Style',
					'selectors'  => array(
						'{{WRAPPER}} .porto-sicon-description',
					),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'desc_font_color',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'group'      => 'Style',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Icon Margin Bottom', 'porto-functionality' ),
					'param_name' => 'icon_margin_bottom',
					'group'      => 'Style',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Spacing between Icon and Title', 'porto-functionality' ),
					'param_name' => 'icon_margin_right',
					'group'      => 'Style',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Title Margin Bottom', 'porto-functionality' ),
					'param_name' => 'title_margin_bottom',
					'group'      => 'Style',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Header Margin Bottom', 'porto-functionality' ),
					'param_name' => 'sub_title_margin_bottom',
					'group'      => 'Style',
				),
				array(
					'type'             => 'css_editor',
					'heading'          => __( 'Css', 'porto-functionality' ),
					'param_name'       => 'css_info_box',
					'group'            => __( 'Design ', 'porto-functionality' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column no-vc-background no-vc-border creative_link_css_editor',
				),
				$animation_type1,
				$animation_duration,
				$animation_delay,
			),
		)
	);
}
