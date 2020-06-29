<?php
// Porto 360 Degree Image Viewer

add_shortcode( 'porto_360degree_image_viewer', 'porto_shortcode_360degree_image_viewer' );
add_action( 'vc_after_init', 'porto_load_360degree_image_viewer_shortcode' );

function porto_shortcode_360degree_image_viewer( $atts, $content = null ) {

	ob_start();
	if ( $template = porto_shortcode_template( 'porto_360degree_image_viewer' ) ) {
		include $template;
	}
	return ob_get_clean();
}

function porto_load_360degree_image_viewer_shortcode() {

	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => __( 'Porto 360 Degree Image Viewer', 'porto-functionality' ),
			'base'        => 'porto_360degree_image_viewer',
			'icon'        => 'porto4_vc_360degree_image_viewer',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Add 360 degree image viewer.', 'porto-functionality' ),
			'params'      => array(
				array(
					'type'        => 'attach_image',
					'class'       => '',
					'heading'     => __( 'Source Image', 'porto-functionality' ),
					'param_name'  => 'img_source',
					'admin_label' => true,
					'value'       => '',
				),
				array(
					'type'        => 'attach_image',
					'class'       => '',
					'heading'     => __( 'Preview Image', 'porto-functionality' ),
					'param_name'  => 'img_preview',
					'value'       => '',
				),
				array(
					'type'        => 'number',
					'class'       => '',
					'heading'     => __( 'Frame Count', 'porto-functionality' ),
					'param_name'  => 'frame_count',
					'value'       => 16,
					'min'         => 2,
					'max'         => 360,
					'admin_label' => true,
				),
				array(
					'type'       => 'number',
					'class'      => '',
					'heading'    => __( 'Friction', 'porto-functionality' ),
					'param_name' => 'friction',
					'value'      => 0.33,
					'min'        => 0.01,
					'max'        => 1.00,
					'step'       => 0.01,
					'admin_label' => true,
				),
				$animation_type,
				$animation_duration,
				$animation_delay,
				$custom_class,
			),
		)
	);

	if ( class_exists( 'WPBakeryShortCode' ) ) {
		class WPBakeryShortCode_porto_360degree_image_viewer extends WPBakeryShortCode {
		}
	}
}
