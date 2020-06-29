<?php

// Porto Section Scroll
add_shortcode( 'porto_section_scroll', 'porto_shortcode_section_scroll' );
add_action( 'vc_after_init', 'porto_load_section_scroll_shortcode' );

function porto_shortcode_section_scroll( $atts, $content = null ) {
	ob_start();
	if ( $template = porto_shortcode_template( 'porto_section_scroll' ) ) {
		include $template;
	}
	return ob_get_clean();
}

function porto_load_section_scroll_shortcode() {
	$custom_class = porto_vc_custom_class();

	vc_map(
		array(
			'name'     => 'Porto ' . __( 'Section Scroll', 'porto-functionality' ),
			'base'     => 'porto_section_scroll',
			'category' => __( 'Porto', 'porto-functionality' ),
			'icon'     => 'porto4_vc_section_scroll',
			'params'   => array(
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Dots Navigation', 'porto-functionality' ),
					'param_name' => 'show_dots_nav',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'std'        => 'yes',
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Is Dots Light?', 'porto-functionality' ),
					'param_name' => 'is_light',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'std'        => '',
					'dependency' => array(
						'element'   => 'show_dots_nav',
						'not_empty' => true,
					),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Section Selectors', 'porto-functionality' ),
					'param_name'  => 'section_ids',
					'description' => __( 'Please enter jQuery selectors of scrolling section elements separated by comma such as "#section1, .section2".', 'porto-functionality' ),
					'admin_label' => true,
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Section Titles', 'porto-functionality' ),
					'param_name'  => 'section_titles',
					'description' => __( 'Please enter section titles separated by comma such as "Section 1, Section 2".', 'porto-functionality' ),
					'dependency'  => array(
						'element'   => 'show_dots_nav',
						'not_empty' => true,
					),
				),
				$custom_class,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Section_Scroll' ) ) {
		class WPBakeryShortCode_Porto_Section_Scroll extends WPBakeryShortCode {
		}
	}
}
