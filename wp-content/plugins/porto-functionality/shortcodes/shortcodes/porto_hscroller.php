<?php
/**
 * Porto Horizontal Scroller
 *
 * @since 2.6.0
 */
add_action( 'vc_after_init', 'porto_load_hscroller_shortcode' );

function porto_load_hscroller_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'            => 'Porto ' . __( 'Horizontal Scroller', 'porto-functionality' ),
			'base'            => 'porto_hscroller',
			'category'        => __( 'Porto', 'porto-functionality' ),
			'description'     => __( 'Multiple items horizontal scroll', 'porto-functionality' ),
			'icon'            => 'fas fa-bacon',
			'content_element' => true,
			'as_parent'       => array( 'except' => 'porto_hscroller' ),
			'is_container'    => true,
			'controls'        => 'full',
			'js_view'         => 'VcColumnView',
			'params'          => array(
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Vertical Alignment', 'porto-functionality' ),
					'description' => __( 'Controls item\'s alignment. Choose from Top, Middle, Bottom.', 'porto-functionality' ),
					'param_name'  => 'h_scroller_align',
					'value'       => array(
						__( 'Top', 'porto-functionality' ) => 'flex-start',
						__( 'Middle', 'porto-functionality' ) => 'center',
						__( 'Bottom', 'porto-functionality' ) => 'flex-end',
					),
					'std'         => 'center',
					'selectors'   => array(
						'{{WRAPPER}} .horizontal-scroller-items' => 'align-items: {{VALUE}}',
					),
				),
				array(
					'type'        => 'porto_dimension',
					'heading'     => __( 'Items Spacing', 'porto-functionality' ),
					'description' => __( 'Controls the item\'s spacing.', 'porto-functionality' ),
					'param_name'  => 'items_spacing',
					'responsive'  => true,
					'selectors'   => array(
						'{{WRAPPER}} .horizontal-scroller-items > *' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}}',
					),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Item Count', 'porto-functionality' ),
					'description' => __( 'Controls item counts.', 'porto-functionality' ),
					'param_name'  => 'scroller_count_lg',
					'value'       => array(
						__( '1', 'porto-functionality' ) => '1',
						__( '2', 'porto-functionality' ) => '2',
						__( '3', 'porto-functionality' ) => '3',
						__( '4', 'porto-functionality' ) => '4',
						__( '5', 'porto-functionality' ) => '5',
						__( '6', 'porto-functionality' ) => '6',
					),
					'std'         => '3',
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Item Count( < 992px )', 'porto-functionality' ),
					'description' => __( 'Controls item counts on mobile.', 'porto-functionality' ),
					'param_name'  => 'scroller_count_md',
					'value'       => array(
						__( '1', 'porto-functionality' ) => '1',
						__( '2', 'porto-functionality' ) => '2',
						__( '3', 'porto-functionality' ) => '3',
						__( '4', 'porto-functionality' ) => '4',
						__( '5', 'porto-functionality' ) => '5',
						__( '6', 'porto-functionality' ) => '6',
					),
					'std'         => '1',
				),
				array(
					'type'        => 'porto_dimension',
					'heading'     => __( 'Scroller Padding', 'porto-functionality' ),
					'description' => __( 'Controls padding of scroller wrapper.', 'porto-functionality' ),
					'param_name'  => 'scroller_padding',
					'responsive'  => true,
					'selectors'   => array(
						'{{WRAPPER}} .horizontal-scroller-scroll' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}}',
					),
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Hscroller' ) ) {
		class WPBakeryShortCode_Porto_Hscroller extends WPBakeryShortCodesContainer {
		}
	}
}
