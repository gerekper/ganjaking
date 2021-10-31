<?php
// Porto Icon

add_action( 'vc_after_init', 'porto_load_image_gallery_shortcode' );

function porto_load_image_gallery_shortcode() {

	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	$slider_options = porto_vc_product_slider_fields();
	foreach ( $slider_options as $index => $o ) {
		if ( isset( $o['dependency'] ) && 'view' == $o['dependency']['element'] ) {
			$slider_options[ $index ]['dependency']['value'] = 'slider';
		}
	}

	vc_map(
		array(
			'name'     => __( 'Porto Image Gallery', 'porto-functionality' ),
			'base'     => 'porto_image_gallery',
			'icon'     => 'far fa-images',
			'category' => __( 'Porto', 'porto-functionality' ),
			'params'   => array_merge(
				array(
					array(
						'type'        => 'attach_images',
						'heading'     => esc_html__( 'Add Images', 'porto-functionality' ),
						'param_name'  => 'images',
						'value'       => '',
						'description' => esc_html__( 'Select images from media library.', 'porto-functionality' ),
					),
					array(
						'type'        => 'porto_button_group',
						'param_name'  => 'view',
						'heading'     => esc_html__( 'Layout', 'porto-functionality' ),
						'std'         => 'slider',
						'value'       => array(
							'grid'     => array(
								'title' => esc_html__( 'Grid', 'porto-functionality' ),
							),
							'slider'   => array(
								'title' => esc_html__( 'Slider', 'porto-functionality' ),
							),
							'masonry'  => array(
								'title' => esc_html__( 'Masonry Grid', 'porto-functionality' ),
							),
							'creative' => array(
								'title' => esc_html__( 'Pre defined Grid', 'porto-functionality' ),
							),
						),
						'description' => esc_html__( 'Select certain layout of your gallery: Grid, Slider, Masonry.', 'porto-functionality' ),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Image Size', 'porto-functionality' ),
						'param_name' => 'image_size',
						'value'      => porto_sh_commons( 'image_sizes' ),
						'std'        => '',
						'dependency' => array(
							'element' => 'view',
							'value'   => array( 'grid', 'slider', 'masonry' ),
						),
					),
					array(
						'type'       => 'porto_image_select',
						'heading'    => __( 'Grid Layout', 'porto-functionality' ),
						'param_name' => 'grid_layout',
						'dependency' => array(
							'element' => 'view',
							'value'   => array( 'creative' ),
						),
						'std'        => '1',
						'value'      => porto_sh_commons( 'masonry_layouts' ),
					),
					array(
						'type'       => 'number',
						'heading'    => __( 'Grid Height (px)', 'porto-functionality' ),
						'param_name' => 'grid_height',
						'dependency' => array(
							'element' => 'view',
							'value'   => array( 'creative' ),
						),
						'suffix'     => 'px',
						'std'        => 600,
					),
					array(
						'type'        => 'porto_number',
						'heading'     => __( 'Column Spacing (px)', 'porto-functionality' ),
						'param_name'  => 'spacing',
						'selectors'   => array(
							'{{WRAPPER}}' => '--porto-el-spacing: {{VALUE}}px;',
						),
					),
					array(
						'type'       => 'porto_number',
						'heading'    => __( 'Columns', 'porto-functionality' ),
						'param_name' => 'columns',
						'responsive' => true,
						'value'      => '{"xl":"4"}',
						'dependency' => array(
							'element' => 'view',
							'value'   => array( 'grid', 'slider', 'masonry' ),
						),
					),
					array(
						'type'        => 'porto_button_group',
						'param_name'  => 'v_align',
						'heading'     => esc_html__( 'Vertical Align', 'porto-functionality' ),
						'value'       => array(
							'start'   => array(
								'title' => esc_html__( 'Top', 'porto-functionality' ),
							),
							'center'  => array(
								'title' => esc_html__( 'Middle', 'porto-functionality' ),
							),
							'end'     => array(
								'title' => esc_html__( 'Bottom', 'porto-functionality' ),
							),
							'stretch' => array(
								'title' => esc_html__( 'Stretch', 'porto-functionality' ),
							),
						),
						'description' => esc_html__( 'Choose from top, middle, bottom and stretch in grid layout.', 'porto-functionality' ),
						'dependency' => array(
							'element' => 'view',
							'value'   => array( 'grid', 'slider' ),
						),
					),
					$custom_class,
				),
				$slider_options,
				array(
					$animation_type,
					$animation_duration,
					$animation_delay,
				)
			),
		)
	);

	if ( class_exists( 'WPBakeryShortCode' ) ) {
		class WPBakeryShortCode_porto_image_gallery extends WPBakeryShortCode {
		}
	}
}
