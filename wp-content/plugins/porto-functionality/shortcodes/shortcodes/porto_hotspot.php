<?php

// Porto Hotspot
if ( function_exists( 'register_block_type' ) ) {
	register_block_type(
		'porto/porto-hotspot',
		array(
			'attributes'      => array(
				'type' => array(
					'type' => 'string',
					'default' => 'html',
				),
				'content' => array(
					'type' => 'string',
					'default' => '',
				),
				'id' => array(
					'type' => 'integer',
				),
				'addlinks_pos' => array(
					'type' => 'string',
					'default' => '',
				),
				'block' => array(
					'type' => 'integer',
				),
				'icon' => array(
					'type' => 'string',
					'default' => ''
				),
				'pos' => array(
					'type' => 'string',
					'default' => 'right'
				),
				'x' => array(
					'type' => 'integer',
				),
				'y' => array(
					'type' => 'integer',
				),
				'size' => array(
					'type' => 'string',
				),
				'icon_size' => array(
					'type' => 'string',
				),
				'color' => array(
					'type' => 'string',
				),
				'bg_color' => array(
					'type' => 'string',
				),
				'el_class' => array(
					'type' => 'string',
				),
			),
			'editor_script'   => 'porto_blocks',
			'render_callback' => 'porto_shortcode_hotspot',
		)
	);
}

function porto_shortcode_hotspot( $atts, $content = null ) {
	ob_start();
	if ( $template = porto_shortcode_template( 'porto_hotspot' ) ) {
		if ( isset( $atts['className'] ) ) {
			$atts['el_class'] = $atts['className'];
		}
		include $template;
	}
	return ob_get_clean();
}

add_action( 'vc_after_init', 'porto_load_hotspot_shortcode' );
function porto_load_hotspot_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Hotspot', 'porto-functionality' ),
			'base'        => 'porto_hotspot',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Add hotspots with products, block or html to the image', 'porto-functionality' ),
			'icon'        => 'fas fa-dot-circle',
			'params'      => array(
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => __( 'Content Type', 'porto-functionality' ),
					'param_name' => 'type',
					'value'      => array(
						__( 'HTML', 'porto-functionality' ) => 'html',
						__( 'Product', 'porto-functionality' ) => 'product',
						__( 'Block', 'porto-functionality' ) => 'block',
					),
					'std'        => 'html',
				),
				array(
					'type'       => 'textarea_html',
					'heading'    => __( 'HTML Content', 'porto-functionality' ),
					'param_name' => 'content',
					'value'      => '',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'html' ),
					),
				),
				array(
					'type'       => 'autocomplete',
					'heading'    => __( 'Product', 'js_composer' ),
					'param_name' => 'id',
					'settings'   => array(
						'multiple' => false,
						'sortable' => true,
					),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'product' ),
					),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Product Layout', 'porto-functionality' ),
					'description' => __( 'Select position of add to cart, add to wishlist, quickview.', 'porto-functionality' ),
					'param_name'  => 'addlinks_pos',
					'value'       => porto_sh_commons( 'products_addlinks_pos' ),
					'dependency'  => array(
						'element' => 'type',
						'value'   => array( 'product' ),
					),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Block ID or Slug', 'porto-functionality' ),
					'param_name'  => 'block',
					'admin_label' => true,
					'dependency'  => array(
						'element' => 'type',
						'value'   => array( 'block' ),
					),
				),
				array(
					'type'        => 'dropdown',
					'class'       => '',
					'heading'     => __( 'Icon to display:', 'porto-functionality' ),
					'param_name'  => 'icon_type',
					'value'       => array(
						__( 'Font Awesome', 'porto-functionality' ) => 'fontawesome',
						__( 'Simple Line Icon', 'porto-functionality' ) => 'simpleline',
						__( 'Porto Icon', 'porto-functionality' ) => 'porto',
					),
					'description' => __( 'Use an existing font icon or upload a custom image.', 'porto-functionality' ),
				),
				array(
					'type'       => 'iconpicker',
					'class'      => '',
					'heading'    => __( 'Icon ', 'porto-functionality' ),
					'param_name' => 'icon',
					'value'      => '',
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => array( 'fontawesome' ),
					),
				),
				array(
					'type'       => 'iconpicker',
					'heading'    => __( 'Icon', 'porto-functionality' ),
					'param_name' => 'icon_simpleline',
					'settings'   => array(
						'type'         => 'simpleline',
						'iconsPerPage' => 4000,
					),
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => 'simpleline',
					),
				),
				array(
					'type'       => 'iconpicker',
					'heading'    => __( 'Icon', 'porto-functionality' ),
					'param_name' => 'icon_porto',
					'settings'   => array(
						'type'         => 'porto',
						'iconsPerPage' => 4000,
					),
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => 'porto',
					),
				),
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => __( 'Popup position', 'porto-functionality' ),
					'param_name' => 'pos',
					'value'      => array(
						__( 'Top', 'porto-functionality' ) => 'top',
						__( 'Right', 'porto-functionality' ) => 'right',
						__( 'Bottom', 'porto-functionality' ) => 'bottom',
						__( 'Left', 'porto-functionality' ) => 'left',
					),
					'std'        => 'right',
				),
				array(
					'type'       => 'number',
					'heading'    => __( 'Horizontal Position (%)', 'porto-functionality' ),
					'param_name' => 'x',
					'min'        => 0,
					'max'        => 100,
					'step'       => 1,
				),
				array(
					'type'       => 'number',
					'class'      => '',
					'heading'    => __( 'Vertical Position (%)', 'porto-functionality' ),
					'param_name' => 'y',
					'min'        => 0,
					'max'        => 100,
					'step'       => 1,
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Spot Size', 'porto-functionality' ),
					'description' => __('Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality'),
					'param_name'  => 'size',
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Icon Size', 'porto-functionality' ),
					'description' => __('Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality'),
					'param_name'  => 'icon_size',
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Icon Color', 'porto-functionality' ),
					'param_name' => 'color',
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Background Color', 'porto-functionality' ),
					'param_name' => 'bg_color',
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	add_filter( 'vc_autocomplete_porto_hotspot_id_callback', 'porto_shortcode_product_id_callback', 10, 1 );
	add_filter( 'vc_autocomplete_porto_hotspot_id_render', 'porto_shortcode_product_id_render', 10, 1 );
	add_filter( 'vc_form_fields_render_field_porto_hotspot_id_param_value', 'porto_shortcode_product_id_param_value', 10, 4 );

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Hotspot' ) ) {
		class WPBakeryShortCode_Porto_Hotspot extends WPBakeryShortCode {
		}
	}
}
