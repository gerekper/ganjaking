<?php

// Porto Blog
add_action( 'vc_after_init', 'porto_load_blog_shortcode' );

function porto_load_blog_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();
	$order_by_values    = porto_vc_order_by();
	$order_way_values   = porto_vc_woo_order_way();
	$slider_options     = porto_vc_product_slider_fields();

	$slider_options[0]['dependency'] = array(
		'element' => 'post_layout',
		'value'   => array( 'slider' ),
	);
	$slider_options[5]['dependency'] = array(
		'element' => 'post_layout',
		'value'   => array( 'slider' ),
	);
	unset( $slider_options[8] );
	unset( $slider_options[9] );

	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Blog', 'porto-functionality' ),
			'base'        => 'porto_blog',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Show posts by beautiful layout', 'porto-functionality' ),
			'icon'        => 'far fa-calendar-alt',
			'params'      => array_merge(
				array(
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Title', 'porto-functionality' ),
						'param_name'  => 'title',
						'admin_label' => true,
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Blog Layout', 'porto-functionality' ),
						'param_name'  => 'post_layout',
						'std'         => 'timeline',
						'value'       => porto_sh_commons( 'blog_layout' ),
						'admin_label' => true,
					),
					array(
						'type'       => 'porto_image_select',
						'heading'    => __( 'Grid Layout', 'porto-functionality' ),
						'param_name' => 'grid_layout',
						'dependency' => array(
							'element' => 'post_layout',
							'value'   => array( 'creative' ),
						),
						'std'        => '1',
						'value'      => porto_sh_commons( 'masonry_layouts' ),
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Grid Height', 'porto-functionality' ),
						'param_name' => 'grid_height',
						'dependency' => array(
							'element' => 'post_layout',
							'value'   => array( 'creative' ),
						),
						'suffix'     => '',
						'std'        => '600px',
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Masonry Layout', 'porto-functionality' ),
						'param_name' => 'masonry_layout',
						'dependency' => array(
							'element' => 'post_layout',
							'value'   => array( 'masonry-creative' ),
						),
						'std'        => '1',
						'value'      => array(
							'1' => '1',
						),
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Post Style', 'porto-functionality' ),
						'description' => __( 'Only "Hover Info" and "Hover Info 2" styles are available for "Grid - Creative" Blog Layout, "Simple Grid", "Simple List" and "Widget Style" styles are available for only "Grid" and "Masonry" blog layouts, and "Modern" style is available for only "Grid" and "Slider" layout.', 'porto-functionality' ),
						'param_name'  => 'post_style',
						'dependency'  => array(
							'element' => 'post_layout',
							'value'   => array( 'grid', 'masonry', 'timeline', 'creative', 'masonry-creative', 'slider' ),
						),
						'value'       => array(
							__( 'Theme Options', 'porto-functionality' ) => '',
							__( 'Default', 'porto-functionality' ) => 'default',
							__( 'Default - Date on Image', 'porto-functionality' ) => 'date',
							__( 'Default - Author Picture', 'porto-functionality' ) => 'author',
							__( 'Post Carousel Style', 'porto-functionality' ) => 'related',
							__( 'Hover Info', 'porto-functionality' ) => 'hover_info',
							__( 'Hover Info 2', 'porto-functionality' ) => 'hover_info2',
							__( 'With Borders', 'porto-functionality' ) => 'padding',
							__( 'Simple Grid', 'porto-functionality' ) => 'grid',
							__( 'Simple List', 'porto-functionality' ) => 'list',
							__( 'Widget Style', 'porto-functionality' ) => 'widget',
							__( 'Modern', 'porto-functionality' ) => 'modern',
						),
						'admin_label' => true,
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Post Meta Type', 'porto-functionality' ),
						'param_name' => 'meta_type',
						'dependency' => array(
							'element' => 'post_style',
							'value'   => array( 'list', 'hover_info', 'hover_info2' ),
						),
						'std'        => '',
						'value'      => array(
							__( 'None', 'porto-functionality' ) => '',
							__( 'Show Date', 'porto-functionality' ) => 'date',
							__( 'Show Categories', 'porto-functionality' ) => 'cat',
							__( 'Show Date & Categories', 'porto-functionality' ) => 'both',
						),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Columns', 'porto-functionality' ),
						'param_name' => 'columns',
						'dependency' => array(
							'element' => 'post_layout',
							'value'   => array( 'grid', 'masonry', 'slider' ),
						),
						'std'        => '3',
						'value'      => porto_sh_commons( 'blog_grid_columns' ),
					),
					array(
						'type'       => 'checkbox',
						'heading'    => __( 'No Space Between Posts?', 'porto-functionality' ),
						'param_name' => 'no_spacing',
						'dependency' => array(
							'element' => 'post_layout',
							'value'   => array( 'grid', 'masonry', 'creative', 'masonry-creative' ),
						),
						'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Category IDs or slugs', 'porto-functionality' ),
						'description' => __( 'comma separated list of category ids or slugs', 'porto-functionality' ),
						'param_name'  => 'cats',
						'admin_label' => true,
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Post IDs', 'porto-functionality' ),
						'description' => __( 'comma separated list of post ids', 'porto-functionality' ),
						'param_name'  => 'post_in',
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Posts Count', 'porto-functionality' ),
						'param_name' => 'number',
						'value'      => '8',
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Order by', 'porto-functionality' ),
						'param_name'  => 'orderby',
						'value'       => $order_by_values,
						/* translators: %s: Wordpres codex page */
						'description' => sprintf( __( 'Select how to sort retrieved posts. More at %s.', 'porto-functionality' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Order way', 'porto-functionality' ),
						'param_name'  => 'order',
						'value'       => $order_way_values,
						/* translators: %s: Wordpres codex page */
						'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'porto-functionality' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Excerpt Length', 'porto-functionality' ),
						'param_name' => 'excerpt_length',
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Pagination Style', 'porto-functionality' ),
						'param_name' => 'view_more',
						'value'      => array(
							__( 'No Pagination', 'porto-functionality' ) => '',
							__( 'Show Pagination', 'porto-functionality' ) => 'show',
							__( 'Show Blog Page Link', 'porto-functionality' ) => 'link',
						),
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Extra class name for Archive Link', 'porto-functionality' ),
						'param_name' => 'view_more_class',
						'dependency' => array(
							'element' => 'view_more',
							'value'   => array( 'link' ),
						),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Image Size', 'porto-functionality' ),
						'param_name' => 'image_size',
						'value'      => porto_sh_commons( 'image_sizes' ),
						'std'        => '',
						'dependency' => array(
							'element' => 'post_layout',
							'value'   => array( 'grid', 'masonry', 'timeline', 'slider' ),
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

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Blog' ) ) {
		class WPBakeryShortCode_Porto_Blog extends WPBakeryShortCode {
		}
	}
}
