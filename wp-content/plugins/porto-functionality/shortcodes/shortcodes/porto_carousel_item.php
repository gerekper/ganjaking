<?php

// Porto Carousel Item
add_action( 'vc_after_init', 'porto_load_carousel_item_shortcode' );

function porto_load_carousel_item_shortcode() {
	$custom_class = porto_vc_custom_class();

	vc_map(
		array(
			'name'            => 'Porto ' . __( 'Carousel Item', 'porto-functionality' ),
			'base'            => 'porto_carousel_item',
			'category'        => __( 'Porto', 'porto-functionality' ),
			'icon'            => 'porto_vc_carousel_item',
			'as_parent'       => array( 'except' => 'porto_carousel_item' ),
			'as_child'        => array( 'only' => 'porto_carousel' ),
			'content_element' => true,
			'controls'        => 'full',
			//'is_container' => true,
			'js_view'         => 'VcColumnView',
			'params'          => array(
				array(
					'type'       => 'label',
					'heading'    => __( "You shouldn't add shortcode, when select Lazy Load Image or Fetch Viedo type.", 'porto-functionality' ),
					'param_name' => 'label',
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Type', 'porto-functionality' ),
					'param_name' => 'type',
					'value'      => array(
						__( 'Default', 'porto-functionality' ) => '',
						__( 'Lazy Load Image', 'porto-functionality' ) => 'lazyload',
						__( 'Fetch Video', 'porto-functionality' ) => 'video',
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Vimeo or Youtube Video URL (Link)', 'porto-functionality' ),
					'param_name' => 'video_url',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'video' ),
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Image URL', 'porto-functionality' ),
					'param_name' => 'image_url',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'lazyload' ),
					),
				),
				array(
					'type'       => 'attach_image',
					'heading'    => __( 'Image', 'porto-functionality' ),
					'param_name' => 'image_id',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'lazyload' ),
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Image Size', 'porto-functionality' ),
					'param_name' => 'image_size',
					'dependency'  => array(
						'element' => 'type',
						'value'   => array( 'lazyload' ),
					),
					'value'      => porto_sh_commons( 'image_sizes' ),
					'std'        => '',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Items to Merge', 'porto-functionality' ),
					'param_name' => 'merge_items',
					'value'      => '1',
				),
				$custom_class,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Carousel_Item' ) ) {
		class WPBakeryShortCode_Porto_Carousel_Item extends WPBakeryShortCodesContainer {
		}
	}
}
