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
					'heading'     => __( 'Video Banner Url', 'porto-functionality' ),
					'param_name'  => 'banner_video',
					'dependency'  => array(
						'element' => 'banner_image',
						'value'   => array( '' ),
					),
					'description' => __( 'Please input mp4 video url.', 'porto-functionality' ),
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
					'type'        => 'checkbox',
					'param_name'  => 'add_container',
					'value'       => array(
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
