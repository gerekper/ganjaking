<?php

// Porto Carousel
add_action( 'vc_after_init', 'porto_load_carousel_shortcode' );

function porto_load_carousel_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'            => 'Porto ' . __( 'Carousel', 'porto-functionality' ),
			'base'            => 'porto_carousel',
			'category'        => __( 'Porto', 'porto-functionality' ),
			'description'     => __( 'A multiple page slider', 'porto-functionality' ),
			'icon'            => 'fas fa-ellipsis-h',
			'as_parent'       => array( 'except' => 'porto_carousel' ),
			'content_element' => true,
			'controls'        => 'full',
			//'is_container' => true,
			'js_view'         => 'VcColumnView',
			'params'          => array(
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Stage Padding', 'porto-functionality' ),
					'param_name' => 'stage_padding',
					'value'      => 40,
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show items in stage padding', 'porto-functionality' ),
					'param_name' => 'show_items_padding',
					'dependency' => array(
						'element'   => 'stage_padding',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Item Margin', 'porto-functionality' ),
					'param_name' => 'margin',
					'value'      => 10,
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Auto Play', 'porto-functionality' ),
					'param_name' => 'autoplay',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Auto Play Timeout', 'porto-functionality' ),
					'param_name' => 'autoplay_timeout',
					'dependency' => array(
						'element'   => 'autoplay',
						'not_empty' => true,
					),
					'value'      => 5000,
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Pause on Mouse Hover', 'porto-functionality' ),
					'param_name' => 'autoplay_hover_pause',
					'dependency' => array(
						'element'   => 'autoplay',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Items', 'porto-functionality' ),
					'param_name' => 'items_responsive',
					'responsive' => true,
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Nav', 'porto-functionality' ),
					'param_name' => 'show_nav',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'group'      => __( 'Navigation', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Nav on Hover', 'porto-functionality' ),
					'param_name' => 'show_nav_hover',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'dependency' => array(
						'element'   => 'show_nav',
						'not_empty' => true,
					),
					'group'      => __( 'Navigation', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Nav Position', 'porto-functionality' ),
					'param_name' => 'nav_pos',
					'value'      => array(
						__( 'Middle', 'porto-functionality' ) => '',
						__( 'Middle Inside', 'porto-functionality' ) => 'nav-pos-inside',
						__( 'Middle Outside', 'porto-functionality' ) => 'nav-pos-outside',
						__( 'Top', 'porto-functionality' ) => 'show-nav-title',
						__( 'Bottom', 'porto-functionality' ) => 'nav-bottom',
					),
					'dependency' => array(
						'element'   => 'show_nav',
						'not_empty' => true,
					),
					'group'      => __( 'Navigation', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Nav Type', 'porto-functionality' ),
					'param_name' => 'nav_type',
					'value'      => porto_sh_commons( 'carousel_nav_types' ),
					'dependency' => array(
						'element' => 'nav_pos',
						'value'   => array( '', 'nav-pos-inside', 'nav-pos-outside', 'nav-bottom' ),
					),
					'group'      => __( 'Navigation', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Dots', 'porto-functionality' ),
					'param_name' => 'show_dots',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'group'      => __( 'Navigation', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Dots Position', 'porto-functionality' ),
					'param_name' => 'dots_pos',
					'value'      => array(
						__( 'Outside', 'porto-functionality' ) => '',
						__( 'Inside', 'porto-functionality' ) => 'nav-inside',
						__( 'Top beside title', 'porto-functionality' ) => 'show-dots-title',
					),
					'dependency' => array(
						'element'   => 'show_dots',
						'not_empty' => true,
					),
					'group'      => __( 'Navigation', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Dots Style', 'porto-functionality' ),
					'param_name' => 'dots_style',
					'value'      => array(
						__( 'Default', 'porto-functionality' ) => '',
						__( 'Circle inner dot', 'porto-functionality' ) => 'dots-style-1',
					),
					'dependency' => array(
						'element'   => 'show_dots',
						'not_empty' => true,
					),
					'group'      => __( 'Navigation', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Dots Align', 'porto-functionality' ),
					'param_name' => 'dots_align',
					'value'      => array(
						__( 'Right', 'porto-functionality' )  => '',
						__( 'Center', 'porto-functionality' ) => 'nav-inside-center',
						__( 'Left', 'porto-functionality' )   => 'nav-inside-left',
					),
					'dependency' => array(
						'element' => 'dots_pos',
						'value'   => array( 'nav-inside' ),
					),
					'group'      => __( 'Navigation', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_animation_type',
					'heading'    => __( 'Item Animation In', 'porto-functionality' ),
					'param_name' => 'animate_in',
					'group'      => __( 'Animation', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_animation_type',
					'heading'    => __( 'Item Animation Out', 'porto-functionality' ),
					'param_name' => 'animate_out',
					'group'      => __( 'Animation', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Infinite loop', 'porto-functionality' ),
					'param_name' => 'loop',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'group'      => __( 'Advanced', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Full Screen', 'porto-functionality' ),
					'param_name' => 'fullscreen',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'group'      => __( 'Advanced', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Center Item', 'porto-functionality' ),
					'param_name' => 'center',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'group'      => __( 'Advanced', 'porto-functionality' ),
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Fetch Videos', 'porto-functionality' ),
					'param_name'  => 'video',
					'value'       => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'description' => __( 'Please edit video items using porto carousel item element.', 'porto-functionality' ),
					'group'       => __( 'Advanced', 'porto-functionality' ),
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Lazy Load', 'porto-functionality' ),
					'param_name'  => 'lazyload',
					'value'       => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'description' => __( 'Please edit lazy load images using porto carousel item element or porto interactive banner element.', 'porto-functionality' ),
					'group'       => __( 'Advanced', 'porto-functionality' ),
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Merge Items', 'porto-functionality' ),
					'param_name'  => 'merge',
					'value'       => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'description' => __( 'Please edit merge items using porto carousel item element.', 'porto-functionality' ),
					'group'       => __( 'Advanced', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Merge Fit', 'porto-functionality' ),
					'param_name' => 'mergeFit',
					'std'        => 'yes',
					'dependency' => array(
						'element'   => 'merge',
						'not_empty' => true,
					),
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'group'      => __( 'Advanced', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Merge Fit on Desktop', 'porto-functionality' ),
					'param_name' => 'mergeFit_lg',
					'std'        => 'yes',
					'dependency' => array(
						'element'   => 'merge',
						'not_empty' => true,
					),
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'group'      => __( 'Advanced', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Merge Fit on Tablet', 'porto-functionality' ),
					'param_name' => 'mergeFit_md',
					'std'        => 'yes',
					'dependency' => array(
						'element'   => 'merge',
						'not_empty' => true,
					),
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'group'      => __( 'Advanced', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Merge Fit on Mobile', 'porto-functionality' ),
					'param_name' => 'mergeFit_sm',
					'std'        => 'yes',
					'dependency' => array(
						'element'   => 'merge',
						'not_empty' => true,
					),
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'group'      => __( 'Advanced', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Merge Fit on Mini', 'porto-functionality' ),
					'param_name' => 'mergeFit_xs',
					'std'        => 'yes',
					'dependency' => array(
						'element'   => 'merge',
						'not_empty' => true,
					),
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'group'      => __( 'Advanced', 'porto-functionality' ),
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Carousel' ) ) {
		class WPBakeryShortCode_Porto_Carousel extends WPBakeryShortCodesContainer {
		}
	}
}
