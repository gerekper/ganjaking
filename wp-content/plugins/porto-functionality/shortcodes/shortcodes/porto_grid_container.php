<?php

// Porto Masonry Container
add_action( 'vc_after_init', 'porto_load_grid_container_shortcode' );

function porto_load_grid_container_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Masonry Container', 'porto-functionality' ),
			'base'        => 'porto_grid_container',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Masonry Grid with any elements', 'porto-functionality' ),
			'icon'        => 'porto_vc_grid_container',
			'as_parent'   => array( 'only' => 'porto_grid_item' ),
			'controls'    => 'full',
			//'is_container' => true,
			'js_view'     => 'VcColumnView',
			'params'      => array(
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Layout', 'porto-functionality' ),
					'param_name' => 'layout',
					'value'      => array(
						__( 'Custom Masonry Layout', 'porto-functionality' )  => '',
						__( 'Predefined Grid Layout', 'porto-functionality' ) => 'preset',
					),
				),
				array(
					'type'       => 'porto_image_select',
					'heading'    => __( 'Grid Layout', 'porto-functionality' ),
					'param_name' => 'grid_layout',
					'dependency' => array(
						'element' => 'layout',
						'value'   => array( 'preset' ),
					),
					'std'        => '1',
					'value'      => porto_sh_commons( 'masonry_layouts' ),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Grid Height', 'porto-functionality' ),
					'param_name' => 'grid_height',
					'dependency' => array(
						'element' => 'layout',
						'value'   => array( 'preset' ),
					),
					'suffix'     => '',
					'std'        => '600px',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Gutter Size', 'porto-functionality' ),
					'param_name' => 'gutter_size',
					'value'      => '2%',
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Max Width', 'porto-functionality' ),
					'param_name'  => 'max_width',
					'description' => __( 'Will be show as grid only when window width > max width.', 'porto-functionality' ),
					'value'       => '767px',
					'dependency'  => array(
						'element' => 'layout',
						'value'   => array( '' ),
					),
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Grid_Container' ) ) {
		class WPBakeryShortCode_Porto_Grid_Container extends WPBakeryShortCodesContainer {
		}
	}
}
