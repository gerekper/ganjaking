<?php
// Porto Interactive Banner Layer
add_action( 'vc_after_init', 'porto_load_interactive_banner_layer_shortcode' );

function porto_load_interactive_banner_layer_shortcode() {

	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'                    => __( 'Porto Banner Layer', 'porto-functionality' ),
			'base'                    => 'porto_interactive_banner_layer',
			'icon'                    => 'far fa-address-card',
			'category'                => __( 'Porto', 'porto-functionality' ),
			'description'             => __( 'Displays the interactive banner image with Information', 'porto-functionality' ),
			'as_child'                => array( 'only' => 'porto_interactive_banner' ),
			'as_parent'               => array( 'except' => 'porto_interactive_banner,porto_interactive_banner_layer' ),
			'controls'                => 'full',
			'show_settings_on_create' => true,
			'js_view'                 => 'VcColumnView',
			'params'                  => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Width', 'porto-functionality' ),
					'param_name'  => 'width',
					'description' => __( 'For example: 50%, 100px, 100rem, 50vw, etc.', 'porto-functionality' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Height', 'porto-functionality' ),
					'param_name'  => 'height',
					'description' => __( 'For example: 50%, 100px, 100rem, 50vw, etc.', 'porto-functionality' ),
				),
				array(
					'type'        => 'number',
					'heading'     => __( 'Horizontal Position', 'porto-functionality' ),
					'param_name'  => 'horizontal',
					'value'       => 50,
					'min'         => -50,
					'max'         => 150,
					'step'        => 1,
					'description' => __( '50 is center, 0 is left and 100 is right.', 'porto-functionality' ),
				),
				array(
					'type'        => 'number',
					'class'       => '',
					'heading'     => __( 'Vertical Position', 'porto-functionality' ),
					'param_name'  => 'vertical',
					'value'       => 50,
					'min'         => -50,
					'max'         => 150,
					'step'        => 1,
					'description' => __( '50 is middle, 0 is top and 100 is bottom.', 'porto-functionality' ),
				),
				$animation_type,
				$animation_duration,
				$animation_delay,
				$custom_class,
				array(
					'type'             => 'css_editor',
					'heading'          => __( 'Css', 'porto-functionality' ),
					'param_name'       => 'css_ibanner_layer',
					'group'            => __( 'Design ', 'porto-functionality' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column no-vc-background no-vc-border creative_link_css_editor',
				),
			),
		)
	);

	if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
		class WPBakeryShortCode_Porto_Interactive_Banner_Layer extends WPBakeryShortCodesContainer {
		}
	}
}
