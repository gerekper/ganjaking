<?php

// Porto Section Scroll
add_action( 'vc_after_init', 'porto_load_section_scroll_shortcode' );

function porto_load_section_scroll_shortcode() {
	$custom_class = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Section Scroll', 'porto-functionality' ),
			'base'        => 'porto_section_scroll',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'You can see next or prev section with only one scroll and dots', 'porto-functionality' ),
			'icon'        => 'fas fa-arrows-alt-v',
			'params'      => array(
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
