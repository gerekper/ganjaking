<?php

// Porto Experience Timeline Item
add_action( 'vc_after_init', 'porto_load_experience_timeline_item_shortcode' );

function porto_load_experience_timeline_item_shortcode() {
	$custom_class = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => __( 'Experience Timeline Item', 'porto-functionality' ),
			'base'        => 'porto_experience_timeline_item',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Show events or posts by timeline layouts', 'porto-functionality' ),
			'icon'        => 'fas fa-list-ul',
			'as_child'    => array( 'only' => 'porto_experience_timeline_container' ),
			'params'      => array(
				array(
					'type'       => 'textfield',
					'heading'    => __( 'From', 'porto-functionality' ),
					'param_name' => 'from',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'To', 'porto-functionality' ),
					'param_name' => 'to',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Duration', 'porto-functionality' ),
					'param_name' => 'duration',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Company', 'porto-functionality' ),
					'param_name' => 'company',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Location', 'porto-functionality' ),
					'param_name' => 'location',
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Heading', 'porto-functionality' ),
					'param_name'  => 'heading',
					'admin_label' => true,
				),
				array(
					'type'       => 'textarea_html',
					'heading'    => __( 'Details', 'porto-functionality' ),
					'param_name' => 'content',
				),
				$custom_class,
				array(
					'type'       => 'label',
					'heading'    => __( 'From, To, Duration & Location Settings', 'porto-functionality' ),
					'param_name' => 'label',
					'group'      => 'Typography',
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'param_name' => 'color',
					'group'      => 'Typography',
				),
				array(
					'type'       => 'label',
					'heading'    => __( 'Company Settings', 'porto-functionality' ),
					'param_name' => 'label',
					'group'      => 'Typography',
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'param_name' => 'company_color',
					'group'      => 'Typography',
				),
				array(
					'type'       => 'label',
					'heading'    => __( 'Heading Settings', 'porto-functionality' ),
					'param_name' => 'label',
					'group'      => 'Typography',
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'param_name' => 'heading_color',
					'group'      => 'Typography',
				),

			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Experience_Timeline_Item' ) ) {
		class WPBakeryShortCode_Porto_Experience_Timeline_Item extends WPBakeryShortCode {

		}
	}
}
