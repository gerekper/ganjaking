<?php

// Porto Sort Item
add_action( 'vc_after_init', 'porto_load_sort_item_shortcode' );

function porto_load_sort_item_shortcode() {
	$custom_class = porto_vc_custom_class();

	vc_map(
		array(
			'name'            => 'Porto ' . __( 'Sort Item', 'porto-functionality' ),
			'base'            => 'porto_sort_item',
			'category'        => __( 'Porto', 'porto-functionality' ),
			'description'     => __( 'We can sort of any elements', 'porto-functionality' ),
			'icon'            => 'porto_vc_sort_item',
			'as_parent'       => array( 'except' => 'porto_sort_item' ),
			'as_child'        => array( 'only' => 'porto_sort_container' ),
			'content_element' => true,
			'controls'        => 'full',
			//'is_container' => true,
			'js_view'         => 'VcColumnView',
			'params'          => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Filter Classes', 'porto-functionality' ),
					'param_name'  => 'filter',
					'description' => __( 'Please add several identifying classes like "transition metal".', 'porto-functionality' ),
					'admin_label' => true,
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Popular Order Value', 'porto-functionality' ),
					'param_name'  => 'popular',
					'value'       => '0',
					'admin_label' => true,
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Text Align', 'porto-functionality' ),
					'param_name' => 'align',
					'value'      => porto_sh_commons( 'align' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Width on Large Screen', 'porto-functionality' ),
					'param_name' => 'width_lg',
					'value'      => porto_sh_commons( 'grid_columns' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Width on Medium Screen', 'porto-functionality' ),
					'param_name' => 'width_md',
					'value'      => porto_sh_commons( 'grid_columns' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Width on Small Screen', 'porto-functionality' ),
					'param_name' => 'width_sm',
					'value'      => porto_sh_commons( 'grid_columns' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Width on Extra Small Screen', 'porto-functionality' ),
					'param_name' => 'width_xs',
					'value'      => porto_sh_commons( 'grid_columns' ),
				),
				$custom_class,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Sort_Item' ) ) {
		class WPBakeryShortCode_Porto_Sort_Item extends WPBakeryShortCodesContainer {
		}
	}
}
