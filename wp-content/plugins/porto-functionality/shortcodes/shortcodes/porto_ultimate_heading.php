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
			'params'      => array(
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Title', 'porto-functionality' ),
					'param_name' => 'main_heading',
					'holder'     => 'div',
					'value'      => '',
				),
				array(
					'type'       => 'porto_param_heading',
					'text'       => __( 'Heading Settings', 'porto-functionality' ),
					'param_name' => 'main_heading_typograpy',
					'group'      => 'Typography',
					'class'      => '',
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Use theme default font family?', 'porto-functionality' ),
					'param_name' => 'main_heading_use_theme_fonts',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'std'        => 'yes',
					'group'      => 'Typography',
					'class'      => '',
				),
				array(
					'type'       => 'google_fonts',
					'param_name' => 'main_heading_font',
					'settings'   => array(
						'fields' => array(
							'font_family_description' => __( 'Select Font Family.', 'porto-functionality' ),
							'font_style_description'  => __( 'Select Font Style.', 'porto-functionality' ),
						),
					),
					'dependency' => array(
						'element'            => 'main_heading_use_theme_fonts',
						'value_not_equal_to' => 'yes',
					),
					'group'      => 'Typography',
				),
				array(
					'type'       => 'textfield',
					'class'      => 'font-size',
					'heading'    => __( 'Font size', 'porto-functionality' ),
					'param_name' => 'main_heading_font_size',
					'group'      => 'Typography',
				),
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => __( 'Font Weight', 'porto-functionality' ),
					'param_name' => 'main_heading_font_weight',
					'value'      => array(
						__( 'Default', 'porto-functionality' ) => '',
						'100' => '100',
						'200' => '200',
						'300' => '300',
						'400' => '400',
						'500' => '500',
						'600' => '600',
						'700' => '700',
						'800' => '800',
						'900' => '900',
					),
					'group'      => 'Typography',
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
					'type'       => 'textfield',
					'class'      => 'font-size',
					'heading'    => __( 'Line Height', 'porto-functionality' ),
					'param_name' => 'main_heading_line_height',
					'group'      => 'Typography',
				),

				array(
					'type'       => 'textfield',
					'class'      => 'font-size',
					'heading'    => __( 'Letter Spacing', 'porto-functionality' ),
					'param_name' => 'main_heading_letter_spacing',
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
					'description' => __( 'Default is H2', 'porto-functionality' ),
				),
				array(
					'type'             => 'porto_param_heading',
					'text'             => __( 'Sub Heading Settings', 'porto-functionality' ),
					'param_name'       => 'sub_heading_typograpy',
					'group'            => 'Typography',
					'class'            => '',
					'edit_field_class' => 'vc_column vc_col-sm-12',
				),

				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Use theme default font family?', 'porto-functionality' ),
					'param_name' => 'sub_heading_use_theme_fonts',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'std'        => 'yes',
					'group'      => 'Typography',
					'class'      => '',
				),
				array(
					'type'       => 'google_fonts',
					'param_name' => 'sub_heading_font',
					'settings'   => array(
						'fields' => array(
							'font_family_description' => __( 'Select Font Family.', 'porto-functionality' ),
							'font_style_description'  => __( 'Select Font Style.', 'porto-functionality' ),
						),
					),
					'dependency' => array(
						'element'            => 'sub_heading_use_theme_fonts',
						'value_not_equal_to' => 'yes',
					),
					'group'      => 'Typography',
				),
				array(
					'type'       => 'textfield',
					'class'      => '',
					'heading'    => __( 'Font Size', 'porto-functionality' ),
					'param_name' => 'sub_heading_font_size',
					'group'      => 'Typography',
				),
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => __( 'Font Weight', 'porto-functionality' ),
					'param_name' => 'sub_heading_font_weight',
					'value'      => array(
						__( 'Default', 'porto-functionality' ) => '',
						'100' => '100',
						'200' => '200',
						'300' => '300',
						'400' => '400',
						'500' => '500',
						'600' => '600',
						'700' => '700',
						'800' => '800',
						'900' => '900',
					),
					'group'      => 'Typography',
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
					'type'       => 'textfield',
					'class'      => '',
					'heading'    => __( 'Line Height', 'porto-functionality' ),
					'param_name' => 'sub_heading_line_height',
					'group'      => 'Typography',
				),

				array(
					'type'       => 'textfield',
					'class'      => '',
					'heading'    => __( 'Letter Spacing', 'porto-functionality' ),
					'param_name' => 'sub_heading_letter_spacing',
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
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => __( 'Alignment', 'porto-functionality' ),
					'param_name' => 'alignment',
					'value'      => array(
						__( 'Center', 'porto-functionality' ) => 'center',
						__( 'Left', 'porto-functionality' )   => 'left',
						__( 'Right', 'porto-functionality' )  => 'right',
					),
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
					'description' => __( 'Horizontal line to divide sections', 'porto-functionality' ),
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
				$custom_class,
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
