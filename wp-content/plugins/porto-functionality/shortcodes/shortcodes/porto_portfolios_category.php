<?php
// Porto Portfolios Categories
add_action( 'vc_after_init', 'porto_load_portfolios_category_shortcode' );

function porto_load_portfolios_category_shortcode() {
	//$animation_type = porto_vc_animation_type();
	//$animation_duration = porto_vc_animation_duration();
	//$animation_delay = porto_vc_animation_delay();
	$custom_class = porto_vc_custom_class();
	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Portfolios Categories', 'porto-functionality' ),
			'base'        => 'porto_portfolios_category',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Show portfolio categories by beautiful layouts. e.g. masonry, slider, grid and so on', 'porto-functionality' ),
			'icon'        => 'far fa-folder-open',
			'params'      => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Title', 'porto-functionality' ),
					'param_name'  => 'title',
					'admin_label' => true,
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Category Layout', 'porto-functionality' ),
					'param_name'  => 'category_layout',
					'std'         => 'strip',
					'value'       => array(
						'Strip'    => 'stripes',
						'Parallax' => 'parallax',
						__( 'Simple List', 'porto-functionality' ) => 'list',
					),
					'admin_label' => true,
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Info View Type', 'porto-functionality' ),
					'param_name' => 'info_view',
					'std'        => '',
					'value'      => array(
						__( 'Basic', 'porto-functionality' ) => '',
						__( 'Bottom Info', 'porto-functionality' ) => 'bottom-info',
						__( 'Bottom Info Dark', 'porto-functionality' ) => 'bottom-info-dark',
					),
					'dependency' => array(
						'element' => 'category_layout',
						'value'   => array( 'stripes', 'parallax' ),
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Hover Image Effect', 'porto-functionality' ),
					'param_name' => 'thumb_image',
					'std'        => '',
					'dependency' => array(
						'element' => 'category_layout',
						'value'   => 'stripes',
					),
					'value'      => array(
						__( 'Zoom', 'porto-functionality' ) => 'zoom',
						__( 'Slow Zoom', 'porto-functionality' ) => 'slow-zoom',
						__( 'No Zoom', 'porto-functionality' ) => 'no-zoom',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Portfolios Counter', 'porto-functionality' ),
					'param_name' => 'portfolios_counter',
					'std'        => 'show',
					'value'      => array(
						__( 'Show', 'porto-functionality' ) => 'show',
						__( 'Hide', 'porto-functionality' ) => 'hide',
					),
					'dependency' => array(
						'element' => 'category_layout',
						'value'   => array( 'stripes', 'parallax' ),
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Category Count', 'porto-functionality' ),
					'param_name' => 'number',
					'value'      => '5',
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Category IDs', 'porto-functionality' ),
					'description' => __( 'comma separated list of category ids', 'porto-functionality' ),
					'param_name'  => 'cat_in',
				),
				$custom_class,
			//$animation_type,
			//$animation_duration,
			//$animation_delay
			),
		)
	);
	if ( ! class_exists( 'WPBakeryShortCode_Porto_Portfolios_Category' ) ) {
		class WPBakeryShortCode_Porto_Portfolios_Category extends WPBakeryShortCode {
		}
	}
}
