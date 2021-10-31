<?php

// Porto Concept
add_action( 'vc_after_init', 'porto_load_concept_shortcode' );

function porto_load_concept_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Concept', 'porto-functionality' ),
			'base'        => 'porto_concept',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Show your concept image with cloud', 'porto-functionality' ),
			'icon'        => 'fas fa-smog',
			'params'      => array(
				array(
					'type'       => 'label',
					'heading'    => __( 'Block 1', 'porto-functionality' ) . ': ' . __( 'Input Image URL or Select Image.', 'porto-functionality' ),
					'param_name' => 'label',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Title', 'porto-functionality' ),
					'param_name' => 'title1',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'URL (Link)', 'porto-functionality' ),
					'param_name' => 'link1',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Image URL', 'porto-functionality' ),
					'param_name' => 'image1_url',
				),
				array(
					'type'       => 'attach_image',
					'heading'    => __( 'Image', 'porto-functionality' ),
					'param_name' => 'image1_id',
				),
				array(
					'type'       => 'label',
					'heading'    => __( 'Block 2', 'porto-functionality' ) . ': ' . __( 'Input Image URL or Select Image.', 'porto-functionality' ),
					'param_name' => 'label',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Title', 'porto-functionality' ),
					'param_name' => 'title2',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'URL (Link)', 'porto-functionality' ),
					'param_name' => 'link2',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Image URL', 'porto-functionality' ),
					'param_name' => 'image2_url',
				),
				array(
					'type'       => 'attach_image',
					'heading'    => __( 'Image', 'porto-functionality' ),
					'param_name' => 'image2_id',
				),
				array(
					'type'       => 'label',
					'heading'    => __( 'Block 3', 'porto-functionality' ) . ': ' . __( 'Input Image URL or Select Image.', 'porto-functionality' ),
					'param_name' => 'label',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Title', 'porto-functionality' ),
					'param_name' => 'title3',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'URL (Link)', 'porto-functionality' ),
					'param_name' => 'link3',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Image URL', 'porto-functionality' ),
					'param_name' => 'image3_url',
				),
				array(
					'type'       => 'attach_image',
					'heading'    => __( 'Image', 'porto-functionality' ),
					'param_name' => 'image3_id',
				),
				array(
					'type'       => 'label',
					'heading'    => __( 'Slideshow Block', 'porto-functionality' ) . ': ' . __( 'Input Image URL or Select Image.', 'porto-functionality' ),
					'param_name' => 'label',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Title', 'porto-functionality' ),
					'param_name' => 'title4',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Slide #1 URL (Link)', 'porto-functionality' ),
					'param_name' => 'slide_link1',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Slide #1 Image URL', 'porto-functionality' ),
					'param_name' => 'slide_image1_url',
				),
				array(
					'type'       => 'attach_image',
					'heading'    => __( 'Slide #1 Image', 'porto-functionality' ),
					'param_name' => 'slide_image1_id',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Slide #2 URL (Link)', 'porto-functionality' ),
					'param_name' => 'slide_link2',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Slide #2 Image URL', 'porto-functionality' ),
					'param_name' => 'slide_image2_url',
				),
				array(
					'type'       => 'attach_image',
					'heading'    => __( 'Slide #2 Image', 'porto-functionality' ),
					'param_name' => 'slide_image2_id',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Slide #3 URL (Link)', 'porto-functionality' ),
					'param_name' => 'slide_link3',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Slide #3 Image URL', 'porto-functionality' ),
					'param_name' => 'slide_image3_url',
				),
				array(
					'type'       => 'attach_image',
					'heading'    => __( 'Slide #3 Image', 'porto-functionality' ),
					'param_name' => 'slide_image3_id',
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Concept' ) ) {
		class WPBakeryShortCode_Porto_Concept extends WPBakeryShortCode {
		}
	}
}
