<?php
// Porto Icon

add_action( 'vc_after_init', 'porto_load_image_gallery_shortcode' );

function porto_load_image_gallery_shortcode() {

	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	$slider_options = porto_vc_product_slider_fields();
	foreach ( $slider_options as $index => $o ) {
		if ( isset( $o['dependency'] ) && 'view' == $o['dependency']['element'] ) {
			$slider_options[ $index ]['dependency']['value'] = 'slider';
		}
	}

	vc_map(
		array(
			'name'        => __( 'Porto Image Gallery', 'porto-functionality' ),
			'base'        => 'porto_image_gallery',
			'icon'        => 'far fa-images',
			'description' => __( 'Display the images with porto style.', 'porto-functionality' ),
			'category'    => __( 'Porto', 'porto-functionality' ),
			'params'      => array_merge(
				array(
					array(
						'type'        => 'attach_images',
						'heading'     => esc_html__( 'Add Images', 'porto-functionality' ),
						'param_name'  => 'images',
						'value'       => '',
						'description' => esc_html__( 'Select images from media library.', 'porto-functionality' ),
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'On click image', 'porto-functionality' ),
						'description' => __( 'Select action for click on image.', 'porto-functionality' ),
						'param_name'  => 'click_action',
						'value'       => array(
							__( 'None', 'porto-functionality' ) => '',
							__( 'Link to large image', 'porto-functionality' ) => 'imgurl',
							__( 'Open Lightbox', 'porto-functionality' ) => 'lightbox',
						),
						'std'         => '',
						'dependency'  => array(
							'element'   => 'images',
							'not_empty' => true,
						),
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Hover Effect', 'porto-functionality' ),
						'description' => __( 'Select an effect for hover on image.', 'porto-functionality' ),
						'param_name'  => 'hover_effect',
						'value'       => array(
							__( 'None', 'porto-functionality' ) => '',
							__( 'Zoom', 'porto-functionality' ) => 'zoom',
							__( 'Fade In', 'porto-functionality' ) => 'fadein',
							__( 'Add Overlay', 'porto-functionality' ) => 'overlay',
							__( 'Add Box Shadow', 'porto-functionality' ) => 'boxshadow',
							__( 'Overlay Icon', 'porto-functionality' ) => 'overlay-icon',
							__( 'Effect 1', 'porto-functionality' ) => 'effect-1',
							__( 'Effect 2', 'porto-functionality' ) => 'effect-2',
							__( 'Effect 3', 'porto-functionality' ) => 'effect-3',
							__( 'Effect 4', 'porto-functionality' ) => 'effect-4',
							__( 'Hoverdir', 'porto-functionality' ) => 'hoverdir',
						),
						'dependency'  => array(
							'element'   => 'images',
							'not_empty' => true,
						),
					),

					array(
						'type'        => 'porto_number',
						'heading'     => __( 'Max Width (px)', 'porto-functionality' ),
						'description' => esc_html__( 'This option is available for "Overlay", "Fade In", "Overlay Icon", "Hoverdir" effects.', 'porto-functionality' ),
						'param_name'  => 'mx_width',
						'selectors'   => array(
							'{{WRAPPER}}.porto-gallery img' => 'max-width: {{VALUE}}px; margin-left: auto; margin-right: auto;',
						),
						'dependency'  => array(
							'element' => 'view',
							'value'   => array( 'grid', 'slider' ),
						),
					),
					array(
						'type'        => 'checkbox',
						'heading'     => __( 'Width Auto', 'porto-functionality' ),
						'description' => esc_html__( 'This option is available for "Overlay", "Fade In", "Overlay Icon", "Hoverdir" effects.', 'porto-functionality' ),
						'param_name'  => 'auto_width',
						'std'         => '',
						'dependency'  => array(
							'element' => 'view',
							'value'   => array( 'grid', 'slider' ),
						),
						'selectors'   => array(
							'{{WRAPPER}}.porto-gallery img' => 'width: auto; margin-left: auto; margin-right: auto;',
						),
					),
					array(
						'type'        => 'porto_button_group',
						'param_name'  => 'view',
						'heading'     => esc_html__( 'Layout', 'porto-functionality' ),
						'std'         => 'slider',
						'value'       => array(
							'grid'     => array(
								'title' => esc_html__( 'Grid', 'porto-functionality' ),
							),
							'slider'   => array(
								'title' => esc_html__( 'Slider', 'porto-functionality' ),
							),
							'masonry'  => array(
								'title' => esc_html__( 'Masonry Grid', 'porto-functionality' ),
							),
							'creative' => array(
								'title' => esc_html__( 'Pre defined Grid', 'porto-functionality' ),
							),
						),
						'description' => esc_html__( 'Select certain layout of your gallery: Grid, Slider, Masonry.', 'porto-functionality' ),
						'group'       => __( 'Image Layout', 'porto-functionality' ),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Image Size', 'porto-functionality' ),
						'param_name' => 'image_size',
						'value'      => porto_sh_commons( 'image_sizes' ),
						'std'        => '',
						'dependency' => array(
							'element' => 'view',
							'value'   => array( 'grid', 'slider', 'masonry' ),
						),
						'group'      => __( 'Image Layout', 'porto-functionality' ),
					),
					array(
						'type'       => 'porto_image_select',
						'heading'    => __( 'Grid Layout', 'porto-functionality' ),
						'param_name' => 'grid_layout',
						'dependency' => array(
							'element' => 'view',
							'value'   => array( 'creative' ),
						),
						'std'        => '1',
						'value'      => porto_sh_commons( 'masonry_layouts' ),
						'group'      => __( 'Image Layout', 'porto-functionality' ),
					),
					array(
						'type'       => 'number',
						'heading'    => __( 'Grid Height (px)', 'porto-functionality' ),
						'param_name' => 'grid_height',
						'dependency' => array(
							'element' => 'view',
							'value'   => array( 'creative' ),
						),
						'suffix'     => 'px',
						'std'        => 600,
						'group'      => __( 'Image Layout', 'porto-functionality' ),
					),
					array(
						'type'       => 'porto_number',
						'heading'    => __( 'Column Spacing (px)', 'porto-functionality' ),
						'param_name' => 'spacing',
						'selectors'  => array(
							'{{WRAPPER}}' => '--porto-el-spacing: {{VALUE}}px;',
						),
						'group'      => __( 'Image Layout', 'porto-functionality' ),
					),
					array(
						'type'       => 'porto_number',
						'heading'    => __( 'Columns', 'porto-functionality' ),
						'param_name' => 'columns',
						'responsive' => true,
						'value'      => '{"xl":"4"}',
						'dependency' => array(
							'element' => 'view',
							'value'   => array( 'grid', 'slider', 'masonry' ),
						),
						'group'      => __( 'Image Layout', 'porto-functionality' ),
					),
					array(
						'type'        => 'porto_button_group',
						'param_name'  => 'v_align',
						'heading'     => esc_html__( 'Vertical Align', 'porto-functionality' ),
						'value'       => array(
							'start'   => array(
								'title' => esc_html__( 'Top', 'porto-functionality' ),
							),
							'center'  => array(
								'title' => esc_html__( 'Middle', 'porto-functionality' ),
							),
							'end'     => array(
								'title' => esc_html__( 'Bottom', 'porto-functionality' ),
							),
							'stretch' => array(
								'title' => esc_html__( 'Stretch', 'porto-functionality' ),
							),
						),
						'description' => esc_html__( 'Choose from top, middle, bottom and stretch in grid layout.', 'porto-functionality' ),
						'dependency'  => array(
							'element' => 'view',
							'value'   => array( 'grid', 'slider' ),
						),
						'group'       => __( 'Image Layout', 'porto-functionality' ),
					),
					array(
						'type'       => 'colorpicker',
						'heading'    => __( 'Overlay Background Color', 'porto-functionality' ),
						'param_name' => 'overlay_bgc',
						'dependency' => array(
							'element' => 'hover_effect',
							'value'   => array( 'fadein', 'overlay', 'overlay-icon', 'hoverdir' ),
						),
						'selectors'  => array(
							'{{WRAPPER}}.porto-ig-fadein figure:before, {{WRAPPER}}.porto-ig-overlay figure:before, {{WRAPPER}} .hover-overlay .fill, {{WRAPPER}} .hover-effect-dir .fill' => 'background-color: {{VALUE}};',
						),
						'group'      => __( 'Style Options', 'porto-functionality' ),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Overlay Icon Type', 'porto-functionality' ),
						'param_name' => 'icon_type',
						'value'      => array(
							__( 'Font Awesome', 'porto-functionality' ) => 'fontawesome',
							__( 'Simple Line Icon', 'porto-functionality' ) => 'simpleline',
							__( 'Porto Icon', 'porto-functionality' ) => 'porto',
						),
						'dependency' => array(
							'element' => 'hover_effect',
							'value'   => array( 'overlay-icon', 'hoverdir' ),
						),
						'group'      => __( 'Style Options', 'porto-functionality' ),
					),
					array(
						'type'       => 'iconpicker',
						'heading'    => __( 'Overlay Icon', 'porto-functionality' ),
						'param_name' => 'icon_cl',
						'value'      => '',
						'dependency' => array(
							'element' => 'icon_type',
							'value'   => array( 'fontawesome' ),
						),
						'group'      => __( 'Style Options', 'porto-functionality' ),
					),
					array(
						'type'       => 'iconpicker',
						'heading'    => __( 'Overlay Icon', 'porto-functionality' ),
						'param_name' => 'icon_simpleline',
						'settings'   => array(
							'type'         => 'simpleline',
							'iconsPerPage' => 4000,
						),
						'dependency' => array(
							'element' => 'icon_type',
							'value'   => 'simpleline',
						),
						'group'      => __( 'Style Options', 'porto-functionality' ),
					),
					array(
						'type'       => 'iconpicker',
						'heading'    => __( 'Overlay Icon', 'porto-functionality' ),
						'param_name' => 'icon_porto',
						'settings'   => array(
							'type'         => 'porto',
							'iconsPerPage' => 4000,
						),
						'dependency' => array(
							'element' => 'icon_type',
							'value'   => 'porto',
						),
						'group'      => __( 'Style Options', 'porto-functionality' ),
					),
					array(
						'type'       => 'porto_number',
						'heading'    => __( 'Icon Size', 'porto-functionality' ),
						'param_name' => 'icon_size',
						'units'      => array( 'px', 'rem', 'em' ),
						'dependency' => array(
							'element' => 'hover_effect',
							'value'   => array( 'overlay-icon', 'hoverdir' ),
						),
						'selectors'  => array(
							'{{WRAPPER}} .fill .centered-icon' => 'width: {{VALUE}}{{UNIT}};height: {{VALUE}}{{UNIT}};line-height: {{VALUE}}{{UNIT}};',
						),
						'group'      => __( 'Style Options', 'porto-functionality' ),
					),
					array(
						'type'       => 'porto_number',
						'heading'    => __( 'Icon Font Size', 'porto-functionality' ),
						'param_name' => 'icon_fs',
						'units'      => array( 'px', 'rem', 'em' ),
						'dependency' => array(
							'element' => 'hover_effect',
							'value'   => array( 'overlay-icon', 'hoverdir' ),
						),
						'selectors'  => array(
							'{{WRAPPER}} .fill .centered-icon' => 'font-size: {{VALUE}}{{UNIT}};',
						),
						'group'      => __( 'Style Options', 'porto-functionality' ),
					),
					array(
						'type'       => 'colorpicker',
						'heading'    => __( 'Overlay Icon Background Color', 'porto-functionality' ),
						'param_name' => 'icon_bgc',
						'dependency' => array(
							'element' => 'hover_effect',
							'value'   => array( 'overlay-icon', 'hoverdir' ),
						),
						'selectors'  => array(
							'{{WRAPPER}} .fill .centered-icon' => 'background-color: {{VALUE}};',
						),
						'group'      => __( 'Style Options', 'porto-functionality' ),
					),
					array(
						'type'       => 'colorpicker',
						'heading'    => __( 'Overlay Icon Color', 'porto-functionality' ),
						'param_name' => 'icon_clr',
						'dependency' => array(
							'element' => 'hover_effect',
							'value'   => array( 'overlay-icon', 'hoverdir' ),
						),
						'selectors'  => array(
							'{{WRAPPER}} .fill .centered-icon' => 'color: {{VALUE}};',
						),
						'group'      => __( 'Style Options', 'porto-functionality' ),
					),
					$custom_class,
				),
				$slider_options,
				array(
					$animation_type,
					$animation_duration,
					$animation_delay,
				)
			),
		)
	);

	if ( class_exists( 'WPBakeryShortCode' ) ) {
		class WPBakeryShortCode_porto_image_gallery extends WPBakeryShortCode {
		}
	}
}
