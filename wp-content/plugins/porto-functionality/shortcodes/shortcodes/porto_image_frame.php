<?php

// Porto Image Frame
add_action( 'vc_after_init', 'porto_load_image_frame_shortcode' );

function porto_load_image_frame_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Image Frame', 'porto-functionality' ),
			'base'        => 'porto_image_frame',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'A single image with caption', 'porto-functionality' ),
			'icon'        => 'far fa-image',
			'params'      => array(
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Type', 'porto-functionality' ),
					'param_name' => 'type',
					'value'      => array(
						__( 'Default', 'porto-functionality' ) => '',
						__( 'Hover Style', 'porto-functionality' ) => 'hover-style',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Shape', 'porto-functionality' ),
					'param_name' => 'shape',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( '' ),
					),
					'value'      => array(
						__( 'Rounded', 'porto-functionality' ) => 'rounded',
						__( 'Circle', 'porto-functionality' ) => 'circle',
						__( 'Thumbnail', 'porto-functionality' ) => 'thumbnail',
					),
				),
				array(
					'type'       => 'vc_link',
					'heading'    => __( 'URL (Link)', 'porto-functionality' ),
					'param_name' => 'link',
				),
				array(
					'type'       => 'label',
					'heading'    => __( 'Input Image URL or Select Image.', 'porto-functionality' ),
					'param_name' => 'label',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Image URL', 'porto-functionality' ),
					'param_name' => 'image_url',
				),
				array(
					'type'       => 'attach_image',
					'heading'    => __( 'Image', 'porto-functionality' ),
					'param_name' => 'image_id',
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Image Size', 'porto-functionality' ),
					'param_name' => 'image_size',
					'value'      => porto_sh_commons( 'image_sizes' ),
					'std'        => '',
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Title', 'porto-functionality' ),
					'param_name'  => 'title',
					'dependency'  => array(
						'element' => 'type',
						'value'   => array( 'hover-style' ),
					),
					'admin_label' => true,
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Sub Title', 'porto-functionality' ),
					'param_name'  => 'sub_title',
					'dependency'  => array(
						'element' => 'type',
						'value'   => array( 'hover-style' ),
					),
					'admin_label' => true,
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Date', 'porto-functionality' ),
					'param_name' => 'date',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'hover-style' ),
					),
				),
				array(
					'type'       => 'textarea_html',
					'heading'    => __( 'Description', 'porto-functionality' ),
					'param_name' => 'content',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'hover-style' ),
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'View Type', 'porto-functionality' ),
					'param_name' => 'view_type',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'hover-style' ),
					),
					'value'      => array(
						__( 'Left Info', 'porto-functionality' ) => '',
						__( 'Centered Info', 'porto-functionality' ) => 'centered-info',
						__( 'Bottom Info', 'porto-functionality' ) => 'bottom-info',
						__( 'Bottom Info Dark', 'porto-functionality' ) => 'bottom-info-dark',
						__( 'Hide Info Hover', 'porto-functionality' ) => 'hide-info-hover',
						__( 'Side Image Left', 'porto-functionality' ) => 'side-image',
						__( 'Side Image Right', 'porto-functionality' ) => 'side-image-right',
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Image Max Width (unit: px)', 'porto-functionality' ),
					'param_name' => 'img_width',
					'value'      => '200',
					'dependency' => array(
						'element' => 'view_type',
						'value'   => array( 'side-image', 'side-image-right' ),
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Align', 'porto-functionality' ),
					'param_name' => 'align',
					'value'      => porto_sh_commons( 'align' ),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'hover-style' ),
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Hover Background', 'porto-functionality' ),
					'param_name' => 'hover_bg',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'hover-style' ),
					),
					'value'      => array(
						__( 'Darken', 'porto-functionality' ) => '',
						__( 'Lighten', 'porto-functionality' ) => 'lighten',
						__( 'Transparent', 'porto-functionality' ) => 'hide-wrapper-bg',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Hover Image Effect', 'porto-functionality' ),
					'param_name' => 'hover_img',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'hover-style' ),
					),
					'value'      => array(
						__( 'Zoom', 'porto-functionality' ) => '',
						__( 'No Zoom', 'porto-functionality' ) => 'no-zoom',
						__( 'Push Horizontally', 'porto-functionality' ) => 'push-hor',
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Disable Border', 'porto-functionality' ),
					'param_name' => 'noborders',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'hover-style' ),
					),
					'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Enable Box Shadow', 'porto-functionality' ),
					'param_name' => 'boxshadow',
					'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show URL (Link) Icon', 'porto-functionality' ),
					'param_name' => 'link_icon',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'hover-style' ),
					),
					'std'        => 'yes',
					'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Centered Links', 'porto-functionality' ),
					'param_name' => 'centered_icons',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'hover-style' ),
					),
					'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'param_group',
					'param_name' => 'icons',
					'dependency' => array(
						'element'   => 'centered_icons',
						'not_empty' => true,
					),
					'params'     => array(
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Icon library', 'js_composer' ),
							'value'      => array(
								__( 'Font Awesome', 'porto-functionality' ) => 'fontawesome',
								__( 'Simple Line Icon', 'porto-functionality' ) => 'simpleline',
								__( 'Custom Image Icon', 'porto-functionality' ) => 'image',
							),
							'param_name' => 'icon_type',
						),
						array(
							'type'       => 'attach_image',
							'heading'    => __( 'Select Icon', 'porto-functionality' ),
							'dependency' => array(
								'element' => 'icon_type',
								'value'   => 'image',
							),
							'param_name' => 'icon_image',
						),
						array(
							'type'       => 'iconpicker',
							'heading'    => __( 'Select Icon', 'porto-functionality' ),
							'param_name' => 'icon',
							'dependency' => array(
								'element' => 'icon_type',
								'value'   => 'fontawesome',
							),
						),
						array(
							'type'       => 'iconpicker',
							'heading'    => __( 'Select Icon', 'porto-functionality' ),
							'param_name' => 'icon_simpleline',
							'value'      => '',
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
							'type'       => 'dropdown',
							'heading'    => __( 'Skin Color', 'porto-functionality' ),
							'param_name' => 'skin',
							'value'      => porto_sh_commons( 'colors' ),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Background Color', 'porto-functionality' ),
							'param_name' => 'bg_color',
							'dependency' => array(
								'element' => 'skin',
								'value'   => array( 'custom' ),
							),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Icon Color', 'porto-functionality' ),
							'param_name' => 'icon_color',
							'dependency' => array(
								'element' => 'skin',
								'value'   => array( 'custom' ),
							),
						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Action Type', 'porto-functionality' ),
							'param_name' => 'action',
							'value'      => porto_sh_commons( 'popup_action' ),
						),
						array(
							'type'       => 'vc_link',
							'heading'    => __( 'URL (Link)', 'porto-functionality' ),
							'param_name' => 'open_link',
							'dependency' => array(
								'element' => 'action',
								'value'   => array( 'open_link' ),
							),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Video or Map URL (Link)', 'porto-functionality' ),
							'param_name' => 'popup_iframe',
							'dependency' => array(
								'element' => 'action',
								'value'   => array( 'popup_iframe' ),
							),
						),
						array(
							'type'        => 'textarea',
							'heading'     => __( 'Popup Block', 'porto-functionality' ),
							'param_name'  => 'popup_block',
							'description' => __( 'Please add block slug name.', 'porto-functionality' ),
							'dependency'  => array(
								'element' => 'action',
								'value'   => array( 'popup_block' ),
							),
						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Popup Size', 'porto-functionality' ),
							'param_name' => 'popup_size',
							'dependency' => array(
								'element' => 'action',
								'value'   => array( 'popup_block' ),
							),
							'value'      => array(
								__( 'Medium', 'porto-functionality' ) => 'md',
								__( 'Large', 'porto-functionality' ) => 'lg',
								__( 'Small', 'porto-functionality' ) => 'sm',
								__( 'Extra Small', 'porto-functionality' ) => 'xs',
							),
						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Popup Animation', 'porto-functionality' ),
							'param_name' => 'popup_animation',
							'dependency' => array(
								'element' => 'action',
								'value'   => array( 'popup_block' ),
							),
							'value'      => array(
								__( 'Fade', 'porto-functionality' ) => 'mfp-fade',
								__( 'Zoom', 'porto-functionality' ) => 'mfp-with-zoom',
								__( 'Fade Zoom', 'porto-functionality' ) => 'my-mfp-zoom-in',
								__( 'Fade Slide', 'porto-functionality' ) => 'my-mfp-slide-bottom',
							),
						),
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Social Links', 'porto-functionality' ),
					'param_name' => 'show_socials',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'hover-style' ),
					),
					'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'param_group',
					'param_name' => 'socials',
					'dependency' => array(
						'element'   => 'show_socials',
						'not_empty' => true,
					),
					'params'     => array(
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Icon library', 'js_composer' ),
							'value'      => array(
								__( 'Font Awesome', 'porto-functionality' ) => 'fontawesome',
								__( 'Simple Line Icon', 'porto-functionality' ) => 'simpleline',
								__( 'Custom Image Icon', 'porto-functionality' ) => 'image',
							),
							'param_name' => 'icon_type',
						),
						array(
							'type'       => 'attach_image',
							'heading'    => __( 'Select Icon', 'porto-functionality' ),
							'dependency' => array(
								'element' => 'icon_type',
								'value'   => 'image',
							),
							'param_name' => 'icon_image',
						),
						array(
							'type'       => 'iconpicker',
							'heading'    => __( 'Select Icon', 'porto-functionality' ),
							'param_name' => 'icon',
							'dependency' => array(
								'element' => 'icon_type',
								'value'   => 'fontawesome',
							),
						),
						array(
							'type'       => 'iconpicker',
							'heading'    => __( 'Select Icon', 'porto-functionality' ),
							'param_name' => 'icon_simpleline',
							'value'      => '',
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
							'type'       => 'dropdown',
							'heading'    => __( 'Skin Color', 'porto-functionality' ),
							'param_name' => 'skin',
							'value'      => porto_sh_commons( 'colors' ),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Background Color', 'porto-functionality' ),
							'param_name' => 'bg_color',
							'dependency' => array(
								'element' => 'skin',
								'value'   => array( 'custom' ),
							),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Icon Color', 'porto-functionality' ),
							'param_name' => 'icon_color',
							'dependency' => array(
								'element' => 'skin',
								'value'   => array( 'custom' ),
							),
						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Action Type', 'porto-functionality' ),
							'param_name' => 'action',
							'value'      => porto_sh_commons( 'popup_action' ),
						),
						array(
							'type'       => 'vc_link',
							'heading'    => __( 'URL (Link)', 'porto-functionality' ),
							'param_name' => 'open_link',
							'dependency' => array(
								'element' => 'action',
								'value'   => array( 'open_link' ),
							),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Video or Map URL (Link)', 'porto-functionality' ),
							'param_name' => 'popup_iframe',
							'dependency' => array(
								'element' => 'action',
								'value'   => array( 'popup_iframe' ),
							),
						),
						array(
							'type'        => 'textarea',
							'heading'     => __( 'Popup Block', 'porto-functionality' ),
							'param_name'  => 'popup_block',
							'description' => __( 'Please add block slug name.', 'porto-functionality' ),
							'dependency'  => array(
								'element' => 'action',
								'value'   => array( 'popup_block' ),
							),
						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Popup Size', 'porto-functionality' ),
							'param_name' => 'popup_size',
							'dependency' => array(
								'element' => 'action',
								'value'   => array( 'popup_block' ),
							),
							'value'      => array(
								__( 'Medium', 'porto-functionality' ) => 'md',
								__( 'Large', 'porto-functionality' ) => 'lg',
								__( 'Small', 'porto-functionality' ) => 'sm',
								__( 'Extra Small', 'porto-functionality' ) => 'xs',
							),
						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Popup Animation', 'porto-functionality' ),
							'param_name' => 'popup_animation',
							'dependency' => array(
								'element' => 'action',
								'value'   => array( 'popup_block' ),
							),
							'value'      => array(
								__( 'Fade', 'porto-functionality' ) => 'mfp-fade',
								__( 'Zoom', 'porto-functionality' ) => 'mfp-with-zoom',
								__( 'Fade Zoom', 'porto-functionality' ) => 'my-mfp-zoom-in',
								__( 'Fade Slide', 'porto-functionality' ) => 'my-mfp-slide-bottom',
							),
						),
					),
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Image_Frame' ) ) {
		class WPBakeryShortCode_Porto_Image_Frame extends WPBakeryShortCode {
		}
	}
}
