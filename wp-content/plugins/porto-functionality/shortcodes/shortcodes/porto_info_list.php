<?php
// Porto Info List

add_shortcode( 'porto_info_list', 'porto_shortcode_info_list' );
add_action( 'vc_after_init', 'porto_load_info_list_shortcode' );

function porto_shortcode_info_list( $atts, $content = null ) {

	ob_start();
	if ( $template = porto_shortcode_template( 'porto_info_list' ) ) {
		include $template;
	}
	return ob_get_clean();
}

function porto_load_info_list_shortcode() {

	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'                    => __( 'Porto Info List', 'porto-functionality' ),
			'base'                    => 'porto_info_list',
			'class'                   => 'porto_info_list',
			'icon'                    => 'porto4_vc_info_list',
			'category'                => __( 'Porto', 'porto-functionality' ),
			'as_parent'               => array( 'only' => 'porto_info_list_item' ),
			'description'             => __( 'Text blocks connected together in one list.', 'porto-functionality' ),
			'content_element'         => true,
			'show_settings_on_create' => true,
			'params'                  => array(
				array(
					'type'        => 'colorpicker',
					'class'       => '',
					'heading'     => __( 'Icon Color:', 'porto-functionality' ),
					'param_name'  => 'icon_color',
					'value'       => '#333333',
					'description' => __( 'Select the color for icon.', 'porto-functionality' ),
				),
				array(
					'type'        => 'number',
					'class'       => '',
					'heading'     => __( 'Icon Font Size', 'porto-functionality' ),
					'param_name'  => 'font_size_icon',
					'value'       => '',
					'min'         => 12,
					'max'         => 72,
					'suffix'      => 'px',
					'description' => __( 'Enter value in pixels.', 'porto-functionality' ),
				),
				$custom_class,
			),
			'js_view'                 => 'VcColumnView',
		)
	);

	class WPBakeryShortCode_porto_info_list extends WPBakeryShortCodesContainer {
	}
}
