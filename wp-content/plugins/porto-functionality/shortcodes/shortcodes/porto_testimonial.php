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
					'type'       => 'textfield',
					'heading'    => __( 'Name', 'porto-functionality' ),
					'param_name' => 'name',
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Name Color', 'porto-functionality' ),
					'param_name' => 'name_color',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Role', 'porto-functionality' ),
					'param_name' => 'role',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Company', 'porto-functionality' ),
					'param_name' => 'company',
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Role & Company Color', 'porto-functionality' ),
					'param_name' => 'role_company_color',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Author Link', 'porto-functionality' ),
					'param_name' => 'author_url',
				),
				array(
					'type'       => 'label',
					'heading'    => __( 'Input Photo URL or Select Photo.', 'porto-functionality' ),
					'param_name' => 'label',
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
				),
				array(
					'type'        => 'textarea_html',
					'heading'     => __( 'Quote', 'porto-functionality' ),
					'param_name'  => 'content',
					'admin_label' => true,
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Quote Color', 'porto-functionality' ),
					'param_name' => 'quote_color',
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
					'type'       => 'checkbox',
					'heading'    => __( 'Remove Background', 'porto-functionality' ),
					'param_name' => 'remove_bg',
					'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'dependency' => array(
						'element' => 'view',
						'value'   => array( 'default', 'default2' ),
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show with Alternative Font', 'porto-functionality' ),
					'param_name' => 'alt_font',
					'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'dependency' => array(
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
