<?php
// Porto Icon

add_action( 'vc_after_init', 'porto_load_image_comparison_shortcode' );

function porto_load_image_comparison_shortcode() {

	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => __( 'Porto Image Comparison', 'porto-functionality' ),
			'base'        => 'porto_image_comparison',
			'icon'        => 'far fa-object-ungroup',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Compares two images by moving divider handle.', 'porto-functionality' ),
			'params'      => array(
				array(
					'type'        => 'attach_image',
					'heading'     => __( 'Before Image', 'porto-functionality' ),
					'param_name'  => 'before_img',
					'admin_label' => true,
					'description' => __( 'Upload a before image to display.', 'porto-functionality' ),
				),
				array(
					'type'        => 'attach_image',
					'heading'     => __( 'After Image', 'porto-functionality' ),
					'param_name'  => 'after_img',
					'admin_label' => true,
					'description' => __( 'Upload a after image to display.', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_button_group',
					'heading'    => __( 'Handle Orientation', 'porto-functionality' ),
					'param_name' => 'orientation',
					'value'      => array(
						'horizontal' => array(
							'title' => esc_html__( 'Horizontal', 'porto-functionality' ),
						),
						'vertical'   => array(
							'title' => esc_html__( 'Vertical', 'porto-functionality' ),
						),
					),
					'std'        => 'horizontal',
				),
				array(
					'type'        => 'number',
					'heading'     => __( 'Handle Offset', 'porto-functionality' ),
					'param_name'  => 'offset',
					'value'       => 50,
					'min'         => 0,
					'max'         => 100,
					'step'        => 1,
					'description' => __( 'Controls the left or top position of the handle on page load.', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_button_group',
					'heading'    => __( 'Handle Movement Control', 'porto-functionality' ),
					'param_name' => 'movement',
					'value'      => array(
						'click'       => array(
							'title' => esc_html__( 'Drag & Click', 'porto-functionality' ),
						),
						'handle_only' => array(
							'title' => esc_html__( 'Drag Only', 'porto-functionality' ),
						),
						'hover'       => array(
							'title' => esc_html__( 'Hover', 'porto-functionality' ),
						),
					),
					'std'        => 'click',
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Icon Class (ex: fas fa-pencil-alt)', 'porto-functionality' ),
					'param_name'  => 'icon_cl',
					'value'       => '',
					'description' => __( 'Inputs the css class of the icon which is located in handle.', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Handle Color', 'porto-functionality' ),
					'param_name' => 'handle_color',
					'value'      => '',
					'selectors'  => array(
						'{{WRAPPER}} .porto-image-comparison-handle' => 'color: {{VALUE}};',
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Handle Background Color', 'porto-functionality' ),
					'param_name' => 'handle_bg_color',
					'value'      => '',
					'selectors'  => array(
						'{{WRAPPER}} .porto-image-comparison-handle' => 'background-color: {{VALUE}};',
					),
				),
				$animation_type,
				$animation_duration,
				$animation_delay,
				$custom_class,
			),
		)
	);

	if ( class_exists( 'WPBakeryShortCode' ) ) {
		class WPBakeryShortCode_porto_image_comparison extends WPBakeryShortCode {
		}
	}
}
