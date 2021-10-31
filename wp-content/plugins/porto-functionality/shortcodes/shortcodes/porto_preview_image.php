<?php

// Porto Preview Image
add_action( 'vc_after_init', 'porto_load_preview_image_shortcode' );

function porto_load_preview_image_shortcode() {
	$custom_class = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Preview Image', 'porto-functionality' ),
			'base'        => 'porto_preview_image',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Show preview images with loading frame', 'porto-functionality' ),
			'icon'        => 'fas fa-camera',
			'params'      => array(
				array(
					'type'        => 'vc_link',
					'heading'     => __( 'URL (Link)', 'porto-functionality' ),
					'param_name'  => 'link',
					'description' => __( 'Add link to image.', 'porto-functionality' ),
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
					'type'        => 'checkbox',
					'heading'     => __( 'Fixed Image', 'porto-functionality' ),
					'param_name'  => 'fixed',
					'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'admin_label' => true,
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Fixed Position', 'porto-functionality' ),
					'param_name' => 'fixed_pos',
					'value'      => porto_sh_commons( 'preview_position' ),
					'dependency' => array(
						'element'   => 'fixed',
						'not_empty' => true,
					),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Preview Time', 'porto-functionality' ),
					'param_name'  => 'time',
					'value'       => porto_sh_commons( 'preview_time' ),
					'admin_label' => true,
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Disable Border', 'porto-functionality' ),
					'param_name'  => 'noborders',
					'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'admin_label' => true,
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Enable Box Shadow', 'porto-functionality' ),
					'param_name'  => 'boxshadow',
					'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'admin_label' => true,
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Preview Height', 'porto-functionality' ),
					'param_name' => 'height',
					'value'      => '232px',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Tip Label', 'porto-functionality' ),
					'param_name' => 'tip_label',
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Tip Skin Color', 'porto-functionality' ),
					'param_name' => 'tip_skin',
					'std'        => 'custom',
					'value'      => porto_sh_commons( 'colors' ),
				),
				$custom_class,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Preview_Image' ) ) {
		class WPBakeryShortCode_Porto_Preview_Image extends WPBakeryShortCode {
		}
	}
}
