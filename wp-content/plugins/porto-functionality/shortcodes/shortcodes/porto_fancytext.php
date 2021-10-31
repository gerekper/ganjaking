<?php
// Porto fancytext

add_action( 'vc_after_init', 'porto_load_fancytext_shortcode' );

function porto_load_fancytext_shortcode() {

	$custom_class       = porto_vc_custom_class();
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();

	vc_map(
		array(
			'name'        => __( 'Porto Fancy Text', 'porto-functionality' ),
			'base'        => 'porto_fancytext',
			'class'       => 'porto_fancytext',
			'icon'        => 'fas fa-recycle',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Fancy lines with animation effects.', 'porto-functionality' ),
			'params'      => array(
				array(
					'type'       => 'textfield',
					'param_name' => 'fancytext_prefix',
					'heading'    => __( 'Prefix', 'porto-functionality' ),
					'value'      => '',
				),
				array(
					'type'        => 'textarea',
					'heading'     => __( 'Fancy Text', 'porto-functionality' ),
					'param_name'  => 'fancytext_strings',
					'description' => __( 'Enter each string on a new line', 'porto-functionality' ),
					'admin_label' => true,
				),
				array(
					'type'       => 'textfield',
					'param_name' => 'fancytext_suffix',
					'heading'    => __( 'Suffix', 'porto-functionality' ),
					'value'      => '',
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Alignment', 'porto-functionality' ),
					'param_name' => 'fancytext_align',
					'value'      => array(
						__( 'Center', 'porto-functionality' ) => 'center',
						__( 'Left', 'porto-functionality' )   => 'left',
						__( 'Right', 'porto-functionality' )  => 'right',
					),
				),
				array(
					'type'        => 'number',
					'heading'     => __( 'Pause Time', 'porto-functionality' ),
					'param_name'  => 'ticker_wait_time',
					'min'         => 0,
					'value'       => '2500',
					'suffix'      => __( 'In Miliseconds', 'porto-functionality' ),
					'group'       => 'Advanced Settings',
					'description' => __( 'How long the string should stay visible?', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Animation Effect', 'porto-functionality' ),
					'param_name' => 'animation_effect',
					'group'      => 'Advanced Settings',
					'value'      => array(
						__( 'Slide', 'porto-functionality' )   => 'slide',
						__( 'Letters Type', 'porto-functionality' ) => 'letters type',
						__( 'Letters Scale', 'porto-functionality' ) => 'letters scale',
						__( 'Letters Rotate 1', 'porto-functionality' ) => 'letters rotate-2',
						__( 'Letters Rotate 2', 'porto-functionality' ) => 'letters rotate-3',
						__( 'Push', 'porto-functionality' )    => 'push',
						__( 'Clip', 'porto-functionality' )    => 'clip',
						__( 'Zoom', 'porto-functionality' )    => 'zoom',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Pause on Hover', 'porto-functionality' ),
					'param_name' => 'ticker_hover_pause',
					'value'      => array(
						'No'  => '',
						'Yes' => 'true',
					),
					'group'      => 'Advanced Settings',
				),
				$custom_class,
				array(
					'type'             => 'porto_param_heading',
					'param_name'       => 'fancy_text_typography',
					'text'             => __( 'Fancy Text Settings', 'porto-functionality' ),
					'value'            => '',
					'group'            => 'Typography',
					'class'            => 'porto-param-heading',
					'edit_field_class' => 'porto-param-heading-wrapper no-top-margin vc_column vc_col-sm-12',
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Use theme default font family?', 'porto-functionality' ),
					'param_name' => 'strings_use_theme_fonts',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'std'        => 'yes',
					'group'      => 'Typography',
					'class'      => '',
				),
				array(
					'type'       => 'google_fonts',
					'param_name' => 'strings_google_font',
					'settings'   => array(
						'fields' => array(
							'font_family_description' => __( 'Select Font Family.', 'porto-functionality' ),
							'font_style_description'  => __( 'Select Font Style.', 'porto-functionality' ),
						),
					),
					'dependency' => array(
						'element'            => 'strings_use_theme_fonts',
						'value_not_equal_to' => 'yes',
					),
					'group'      => 'Typography',
				),
				array(
					'type'       => 'textfield',
					'class'      => '',
					'heading'    => __( 'Font Weight', 'porto-functionality' ),
					'param_name' => 'strings_font_style',
					'group'      => 'Typography',
				),
				array(
					'type'       => 'textfield',
					'class'      => '',
					'heading'    => __( 'Font Size', 'porto-functionality' ),
					'param_name' => 'strings_font_size',
					'group'      => 'Typography',
				),
				array(
					'type'       => 'textfield',
					'class'      => '',
					'heading'    => __( 'Line Height', 'porto-functionality' ),
					'param_name' => 'strings_line_height',
					'group'      => 'Typography',
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Fancy Text Color', 'porto-functionality' ),
					'param_name' => 'fancytext_color',
					'group'      => 'Advanced Settings',
					'group'      => 'Typography',
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Fancy Text Background', 'porto-functionality' ),
					'param_name' => 'ticker_background',
					'group'      => 'Advanced Settings',
					'group'      => 'Typography',
				),
				array(
					'type'             => 'porto_param_heading',
					'param_name'       => 'fancy_prefsuf_text_typography',
					'text'             => __( 'Prefix Suffix Text Settings', 'porto-functionality' ),
					'value'            => '',
					'group'            => 'Typography',
					'class'            => 'porto-param-heading',
					'edit_field_class' => 'porto-param-heading-wrapper no-top-margin vc_column vc_col-sm-12',
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Use theme default font family?', 'porto-functionality' ),
					'param_name' => 'prefsuf_use_theme_fonts',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'std'        => 'yes',
					'group'      => 'Typography',
					'class'      => '',
				),
				array(
					'type'       => 'google_fonts',
					'param_name' => 'prefsuf_google_font',
					'settings'   => array(
						'fields' => array(
							'font_family_description' => __( 'Select Font Family.', 'porto-functionality' ),
							'font_style_description'  => __( 'Select Font Style.', 'porto-functionality' ),
						),
					),
					'dependency' => array(
						'element'            => 'prefsuf_use_theme_fonts',
						'value_not_equal_to' => 'yes',
					),
					'group'      => 'Typography',
				),
				array(
					'type'       => 'textfield',
					'class'      => '',
					'heading'    => __( 'Font Weight', 'porto-functionality' ),
					'param_name' => 'prefsuf_font_style',
					'group'      => 'Typography',
				),
				array(
					'type'       => 'textfield',
					'class'      => '',
					'heading'    => __( 'Font Size', 'porto-functionality' ),
					'param_name' => 'prefix_suffix_font_size',
					'group'      => 'Typography',
				),
				array(
					'type'       => 'textfield',
					'class'      => '',
					'heading'    => __( 'Line Height', 'porto-functionality' ),
					'param_name' => 'prefix_suffix_line_height',
					'group'      => 'Typography',
				),
				array(
					'type'       => 'colorpicker',
					'class'      => '',
					'heading'    => __( 'Prefix & Suffix Text Color', 'porto-functionality' ),
					'param_name' => 'sufpref_color',
					'value'      => '',
					'group'      => 'Typography',
				),
				array(
					'type'       => 'colorpicker',
					'class'      => '',
					'heading'    => __( 'Prefix & Suffix Background Color', 'porto-functionality' ),
					'param_name' => 'sufpref_bg_color',
					'value'      => '',
					'group'      => 'Typography',
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Markup', 'porto-functionality' ),
					'param_name' => 'fancytext_tag',
					'value'      => array(
						__( 'div', 'porto-functionality' ) => 'div',
						__( 'H1', 'porto-functionality' )  => 'h1',
						__( 'H2', 'porto-functionality' )  => 'h2',
						__( 'H3', 'porto-functionality' )  => 'h3',
						__( 'H4', 'porto-functionality' )  => 'h4',
						__( 'H5', 'porto-functionality' )  => 'h5',
						__( 'H6', 'porto-functionality' )  => 'h6',
					),
					'std'        => 'h2',
					'group'      => 'Typography',
				),
				array(
					'type'             => 'css_editor',
					'heading'          => __( 'Css', 'porto-functionality' ),
					'param_name'       => 'css_fancy_design',
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
		class WPBakeryShortCode_porto_fancytext extends WPBakeryShortCode {
		}
	}
}
