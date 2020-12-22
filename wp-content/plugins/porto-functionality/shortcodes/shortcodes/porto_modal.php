<?php
// Porto modal

add_action( 'vc_after_init', 'porto_load_modal_shortcode' );

function porto_load_modal_shortcode() {

	$custom_class = porto_vc_custom_class();

	vc_map(
		array(
			'name'                    => __( 'Porto Modal Box', 'porto-functionality' ),
			'base'                    => 'porto_modal',
			'icon'                    => 'far fa-window-restore',
			'class'                   => 'porto_modal',
			'category'                => __( 'Porto', 'porto-functionality' ),
			'description'             => __( 'Adds bootstrap modal box in your content', 'porto-functionality' ),
			'controls'                => 'full',
			'show_settings_on_create' => true,
			'params'                  => array(
				// Add some description
				array(
					'type'        => 'dropdown',
					'heading'     => __( "What's in Modal Popup?", 'porto-functionality' ),
					'param_name'  => 'modal_contain',
					'value'       => array(
						__( 'Miscellaneous Things', 'porto-functionality' ) => 'html',
						__( 'Youtube Video', 'porto-functionality' ) => 'youtube',
						__( 'Vimeo Video', 'porto-functionality' ) => 'vimeo',
					),
					'description' => __( "Please put the embed code in the content for videos, eg: <a href='http://bsf.io/kuv3-' target='_blank'>http://bsf.io/kuv3-</a><br>For hosted video - Add any video with WordPress media uploader or with <a href='https://codex.wordpress.org/Video_Shortcode' target='_blank'>[video]</a> shortcode.", 'porto-functionality' ),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Youtube URL', 'porto-functionality' ),
					'param_name' => 'youtube_url',
					'value'      => '',
					'dependency' => array(
						'element' => 'modal_contain',
						'value'   => array( 'youtube' ),
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Vimeo URL', 'porto-functionality' ),
					'param_name' => 'vimeo_url',
					'value'      => '',
					'dependency' => array(
						'element' => 'modal_contain',
						'value'   => array( 'vimeo' ),
					),
				),
				array(
					'type'             => 'textarea_html',
					'heading'          => __( 'Modal Content', 'porto-functionality' ),
					'param_name'       => 'content',
					'value'            => '',
					'description'      => __( 'Content that will be displayed in Modal Popup.', 'porto-functionality' ),
					'edit_field_class' => 'vc_col-xs-12 vc_column wpb_el_type_textarea_html vc_wrapper-param-type-textarea_html vc_shortcode-param',
					'dependency'       => array(
						'element' => 'modal_contain',
						'value'   => array( 'html' ),
					),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Display Modal On -', 'porto-functionality' ),
					'param_name'  => 'modal_on',
					'value'       => array(
						__( 'On Page Load', 'porto-functionality' ) => 'onload',
						__( 'Image', 'porto-functionality' ) => 'image',
						__( 'Selector', 'porto-functionality' ) => 'custom-selector',
					),
					'description' => __( 'When should the popup be initiated?', 'porto-functionality' ),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Timeout in seconds', 'porto-functionality' ),
					'param_name' => 'modal_onload_timeout',
					'value'      => '',
					'dependency' => array(
						'element' => 'modal_on',
						'value'   => array( 'onload' ),
					),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Class and/or ID', 'porto-functionality' ),
					'param_name'  => 'modal_on_selector',
					'description' => __( 'Add .Class and/or #ID to open your modal. Multiple ID or Classes separated by comma', 'porto-functionality' ),
					'value'       => '',
					'dependency'  => array(
						'element' => 'modal_on',
						'value'   => array( 'custom-selector' ),
					),
				),
				array(
					'type'        => 'attach_image',
					'heading'     => __( 'Upload Image', 'porto-functionality' ),
					'param_name'  => 'btn_img',
					'admin_label' => true,
					'description' => __( 'Upload the custom image / image banner.', 'porto-functionality' ),
					'dependency'  => array(
						'element' => 'modal_on',
						'value'   => array( 'image' ),
					),
				),
				// Modal Style
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Modal Box Style', 'porto-functionality' ),
					'param_name' => 'modal_style',
					'value'      => array(
						__( 'Fade', 'porto-functionality' ) => 'mfp-fade',
						__( 'Zoom in', 'porto-functionality' ) => 'my-mfp-zoom-in',
					),
					'dependency' => array(
						'element' => 'modal_contain',
						'value'   => array( 'html' ),
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Overlay Background Color', 'porto-functionality' ),
					'param_name' => 'overlay_bg_color',
					'value'      => '',
				),
				array(
					'type'        => 'number',
					'heading'     => __( 'Overlay Background Opacity', 'porto-functionality' ),
					'param_name'  => 'overlay_bg_opacity',
					'value'       => 80,
					'min'         => 10,
					'max'         => 100,
					'suffix'      => '%',
					'description' => __( 'Select opacity of overlay background.', 'porto-functionality' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Extra Class (Button/Image)', 'porto-functionality' ),
					'param_name'  => 'init_extra_class',
					'admin_label' => true,
					'value'       => '',
					'description' => __( 'Provide ex class for this button/image.', 'porto-functionality' ),
					'dependency'  => array(
						'element' => 'modal_on',
						'value'   => array( 'image' ),
					),
				),
				$custom_class,
			), // end params array
		) // end vc_map array
	); // end vc_map

	if ( class_exists( 'WPBakeryShortCode' ) ) {
		class WPBakeryShortCode_porto_modal extends WPBakeryShortCode {
		}
	}
}
