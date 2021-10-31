<?php

// Porto Sort Filter
add_action( 'vc_after_init', 'porto_load_sort_filter_shortcode' );

function porto_load_sort_filter_shortcode() {
	$custom_class = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Sort Filter', 'porto-functionality' ),
			'base'        => 'porto_sort_filter',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'We can sort of any elements', 'porto-functionality' ),
			'icon'        => 'fas fa-filter',
			'as_child'    => array( 'only' => 'porto_sort_filters' ),
			'params'      => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Label', 'porto-functionality' ),
					'param_name'  => 'label',
					'admin_label' => true,
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Sort By', 'porto-functionality' ),
					'param_name' => 'sort_by',
					'std'        => 'popular',
					'value'      => porto_sh_commons( 'sort_by' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Filter By', 'porto-functionality' ),
					'param_name'  => 'filter_by',
					'description' => __( 'Please add several identifying classes like "*" or ".transition, .metal".', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Active Filter', 'porto-functionality' ),
					'param_name' => 'active',
					'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				),
				$custom_class,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Sort_Filter' ) ) {
		class WPBakeryShortCode_Porto_Sort_Filter extends WPBakeryShortCode {
		}
	}
}
