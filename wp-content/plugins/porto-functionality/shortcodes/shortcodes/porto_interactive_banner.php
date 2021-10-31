<?php
// Porto interactive_banner
add_action( 'vc_after_init', 'porto_load_interactive_banner_shortcode' );

function porto_load_interactive_banner_shortcode() {

	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'                    => __( 'Porto Banner', 'porto-functionality' ),
			'base'                    => 'porto_interactive_banner',
			'class'                   => 'porto_interactive_banner',
			'icon'                    => 'far fa-address-card',
			'category'                => __( 'Porto', 'porto-functionality' ),
			'description'             => __( 'Displays the interactive banner image with Information', 'porto-functionality' ),
			'as_parent'               => array( 'only' => 'porto_interactive_banner_layer' ),
			'controls'                => 'full',
			'show_settings_on_create' => true,
			'js_view'                 => 'VcColumnView',
			'params'                  => array(
				array(
					'type'        => 'attach_image',
					'class'       => '',
					'heading'     => __( 'Banner Image', 'porto-functionality' ),
					'param_name'  => 'banner_image',
					'value'       => '',
					'description' => __( 'Upload the image for this banner', 'porto-functionality' ),
				),
				array(
					'type'        => 'checkbox',
					'heading'     => '',
					'param_name'  => 'lazyload',
					'value'       => array(
						__( 'Lazy Load Image', 'porto-functionality' ) => 'enable',
					),
					'description' => __( 'If you have this element in Porto Carousel, please check "Lazy Load" option in Porto Carousel element.', 'porto-functionality' ),
					'dependency'  => array(
						'element'   => 'banner_image',
						'not_empty' => true,
					),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Video Banner Url (mp4, ogg, webm, Youtube or Vimeo url)', 'porto-functionality' ),
					'param_name'  => 'banner_video',
					'description' => __( 'Banner Image is used as a poster.', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'param_name' => 'enable_sound',
					'heading'    => __( 'Enable Sound?', 'porto-functionality' ),
					'value'      => array(
						__( 'Enable Sound?', 'porto-functionality' ) => 'yes',
					),
					'dependency'  => array(
						'element'   => 'banner_video',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Background Color', 'porto-functionality' ),
					'param_name' => 'banner_color_bg',
				),
				array(
					'type'        => 'vc_link',
					'class'       => '',
					'heading'     => __( 'Link ', 'porto-functionality' ),
					'param_name'  => 'banner_link',
					'value'       => '',
					'description' => __( 'Add link / select existing page to link to this banner', 'porto-functionality' ),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Min Height', 'porto-functionality' ),
					'param_name' => 'min_height',
				),
				array(
					'type'       => 'checkbox',
					'param_name' => 'add_container',
					'heading'    => __( 'Add Container', 'porto-functionality' ),
					'value'      => array(
						__( 'Add Container', 'porto-functionality' ) => 'yes',
					),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Parallax', 'porto-functionality' ),
					'param_name'  => 'parallax',
					'description' => __( 'Enter parallax speed ratio if you want to use parallax effect. (Note: Default value is 1.5, min value is 1. Leave empty if you don\'t want.)', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => esc_html__( 'Banner Effect', 'porto-functionality' ),
					'param_name' => 'banner_effect',
					'value'      => array(
						esc_html__( 'None', 'porto-functionality' ) => 'none',
						esc_html__( 'kenBurnsToRight', 'porto-functionality' ) => 'kenBurnsToRight',
						esc_html__( 'kenBurnsToLeft', 'porto-functionality' ) => 'kenBurnsToLeft',
						esc_html__( 'kenBurnsToLeftTop', 'porto-functionality' ) => 'kenBurnsToLeftTop',
						esc_html__( 'kenBurnsToRightTop', 'porto-functionality' ) => 'kenBurnsToRightTop',
					),
					'std'        => 'none',
				),
				array(
					'type'       => 'textfield',
					'heading'    => esc_html__( 'Banner Effect Duration(s)', 'porto-functionality' ),
					'param_name' => 'effect_duration',
					'std'        => '30',
					'dependency' => array(
						'element' => 'banner_effect',
						'value_not_equal_to' => 'none',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => esc_html__( 'Particle Effect', 'porto-functionality' ),
					'param_name' => 'particle_effect',
					'value'      => array(
						esc_html__( 'None', 'porto-functionality' )     => '',
						esc_html__( 'Snowfall', 'porto-functionality' ) => 'snowfall',
						esc_html__( 'Sparkle', 'porto-functionality' )  => 'sparkle',
					),
					'std'        => '',
				),
				array(
					'type'        => 'textfield',
					'class'       => '',
					'heading'     => __( 'Title ', 'porto-functionality' ),
					'param_name'  => 'banner_title',
					'admin_label' => true,
					'value'       => '',
					'description' => __( 'We recommend using banner layer child element instead of this field.', 'porto-functionality' ),
					'group'       => 'Deprecated',
				),
				array(
					'type'        => 'textarea_html',
					'class'       => '',
					'heading'     => __( 'Description', 'porto-functionality' ),
					'param_name'  => 'content',
					'value'       => '',
					'description' => __( 'We recommend using banner layer child element instead of this field.', 'porto-functionality' ),
					'group'       => 'Deprecated',
				),
				$custom_class,
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => __( 'Hover Effect ', 'porto-functionality' ),
					'param_name' => 'banner_style',
					'value'      => array(
						__( 'None', 'porto-functionality' ) => '',
						__( 'Zoom', 'porto-functionality' ) => 'zoom',
						__( 'Content Fade In', 'porto-functionality' ) => 'fadein',
						__( 'Content Fade Out', 'porto-functionality' ) => 'fadeout',
						__( 'Add Overlay', 'porto-functionality' ) => 'overlay',
						__( 'Add Box Shadow', 'porto-functionality' ) => 'boxshadow',
						__( 'Effect 1', 'porto-functionality' ) => 'effect-1',
						__( 'Effect 2', 'porto-functionality' ) => 'effect-2',
						__( 'Effect 3', 'porto-functionality' ) => 'effect-3',
						__( 'Effect 4', 'porto-functionality' ) => 'effect-4',
					),
					'group'      => 'Hover',
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Overlay Color', 'porto-functionality' ),
					'param_name' => 'overlay_color',
					'dependency' => array(
						'element' => 'banner_style',
						'value'   => array( 'overlay' ),
					),
					'group'      => 'Hover',
				),
				array(
					'type'        => 'number',
					'class'       => '',
					'heading'     => __( 'Overlay Opacity', 'porto-functionality' ),
					'param_name'  => 'overlay_opacity',
					'value'       => 0.08,
					'min'         => 0.00,
					'max'         => 1.00,
					'step'        => 0.01,
					'suffix'      => '',
					'description' => __( 'Enter value between 0.0 to 1 (0 is maximum transparency, while 1 is lowest)', 'porto-functionality' ),
					'dependency' => array(
						'element' => 'banner_style',
						'value'   => array( 'overlay' ),
					),
					'group'       => 'Hover',
				),
				array(
					'type'       => 'porto_boxshadow',
					'heading'    => __( 'Box Shadow', 'porto-functionality' ),
					'param_name' => 'box_shadow',
					'unit'       => 'px',
					'positions'  => array(
						__( 'Horizontal', 'porto-functionality' ) => '',
						__( 'Vertical', 'porto-functionality' ) => '',
						__( 'Blur', 'porto-functionality' )   => '',
						__( 'Spread', 'porto-functionality' ) => '',
					),
					'dependency' => array(
						'element' => 'banner_style',
						'value'   => array( 'boxshadow' ),
					),
					'group'      => 'Hover',
				),
				array(
					'type'        => 'number',
					'class'       => '',
					'heading'     => __( 'Image Opacity', 'porto-functionality' ),
					'param_name'  => 'image_opacity',
					'value'       => 1,
					'min'         => 0.0,
					'max'         => 1.0,
					'step'        => 0.1,
					'suffix'      => '',
					'description' => __( 'Enter value between 0.0 to 1 (0 is maximum transparency, while 1 is lowest)', 'porto-functionality' ),
					'group'       => 'Hover',
				),
				array(
					'type'        => 'number',
					'class'       => '',
					'heading'     => __( 'Image Opacity on Hover', 'porto-functionality' ),
					'param_name'  => 'image_opacity_on_hover',
					'value'       => 1,
					'min'         => 0.0,
					'max'         => 1.0,
					'step'        => 0.1,
					'suffix'      => '',
					'description' => __( 'Enter value between 0.0 to 1 (0 is maximum transparency, while 1 is lowest)', 'porto-functionality' ),
					'group'       => 'Hover',
				),
				array(
					'type'             => 'css_editor',
					'heading'          => __( 'Css', 'porto-functionality' ),
					'param_name'       => 'css_ibanner',
					'group'            => __( 'Design ', 'porto-functionality' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column no-vc-background no-vc-border creative_link_css_editor',
				),
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
		class WPBakeryShortCode_Porto_Interactive_Banner extends WPBakeryShortCodesContainer {
		}
	}

}
