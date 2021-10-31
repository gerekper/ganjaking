<?php

if ( function_exists( 'register_block_type' ) ) {
	register_block_type(
		'porto/porto-section',
		array(
			'editor_script'   => 'porto_blocks',
			'render_callback' => function( $atts, $content = null ) {
				$atts = shortcode_atts(
					array(
						'add_container'         => '',
						'bg_color'              => '',
						'bg_img'                => '',
						'bg_img_url'            => '',
						'bg_repeat'             => '',
						'bg_pos'                => '',
						'bg_size'               => '',
						'parallax_speed'        => '',
						'bg_video'              => '',
						'tag'                   => 'section',
						'align'                 => '',
						'top_divider_type'      => '',
						'top_divider_custom'    => '',
						'top_divider_color'     => '',
						'top_divider_height'    => '',
						'top_divider_flip'      => '',
						'top_divider_invert'    => '',
						'top_divider_class'     => '',
						'bottom_divider_type'   => '',
						'bottom_divider_custom' => '',
						'bottom_divider_color'  => '',
						'bottom_divider_height' => '',
						'bottom_divider_flip'   => '',
						'bottom_divider_invert' => '',
						'bottom_divider_class'  => '',
						'className'             => '',
					),
					$atts
				);
				if( 'none' == $atts['top_divider_type'] ) {
					$atts['top_divider_type'] = '';
				}
				if( 'none' == $atts['bottom_divider_type'] ) {
					$atts['bottom_divider_type'] = '';
				}
				$classes = array( 'vc_section', 'porto-section' );

				if( ! ( empty( $atts['top_divider_type'] ) && empty( $atts['bottom_divider_type'] ) ) ) {
					$classes[] = 'section-with-shape-divider';
				}

				$attrs   = '';
				$style   = '';
				if ( $atts['bg_video'] ) {
					wp_enqueue_script( 'jquery-vide' );
					$classes[] = 'section-video';
					$attrs .= ' data-video-path="' . esc_url( str_replace( '.mp4', '', $atts['bg_video'] ) ) . '"';
					$attrs .= ' data-plugin-video-background';
					$attrs .= ' data-plugin-options="{\'posterType\': \'jpg\', \'position\': \'50% 50%\', \'overlay\': true}"';
				} elseif ( $atts['bg_img_url'] ) {
					if ( $atts['parallax_speed'] ) {
						wp_enqueue_script( 'skrollr' );
						$classes[] = 'section-parallax';
						$attrs    .= ' data-plugin-parallax data-plugin-options="' . esc_attr( json_encode( array( 'speed' => $atts['parallax_speed'] ) ) ) . '" data-image-src="' . esc_url( $atts['bg_img_url'] ) . '"';
					} else {
						$style .= 'background-image: url(' . esc_url( $atts['bg_img_url'] ) . ');';
					}
					if ( $atts['bg_repeat'] ) {
						$style .= 'background-repeat:' . $atts['bg_repeat'] . ';';
					}
					if ( $atts['bg_pos'] ) {
						$style .= 'background-position:' . $atts['bg_pos'] . ';';
					}
					if ( $atts['bg_size'] ) {
						$style .= 'background-size:' . $atts['bg_size'] . ';';
					}
				}
				if ( $atts['bg_color'] ) {
					$style .= 'background-color:' . $atts['bg_color'] . ';';
				}
				if ( $style ) {
					$attrs .= ' style="' . esc_attr( $style ) . '"';
				}

				if ( $atts['add_container'] ) {
					$classes[] = 'porto-inner-container';
				}
				if ( $atts['align'] ) {
					$classes[] = 'align' . $atts['align'];
				}
				if ( $atts['className'] ) {
					$classes[] = trim( $atts['className'] );
				}
				$output = '<' . esc_html( $atts['tag'] ) . ' class="' . esc_attr( implode( ' ', $classes ) ) . '"' . $attrs . '>';

				if( ! empty( $atts['top_divider_type'] ) ) {
					$top_divider_attr = array(
						'shape-divider',
					);
					if( ! empty( $atts['top_divider_class'] ) ) {
						$top_divider_attr[] = $atts['top_divider_class'];
					}
					if( ! empty( $atts['top_divider_invert'] ) && ! empty( $atts['top_divider_flip'] ) ) {
						$top_divider_attr[] = 'shape-divider-reverse-xy';		
					} elseif( ! empty( $atts['top_divider_invert'] ) ) {
						$top_divider_attr[] = 'shape-divider-reverse-x';
					} elseif( ! empty( $atts['top_divider_flip'] ) ) {
						$top_divider_attr[] = 'shape-divider-reverse-y';
					}
					if ( ! empty( $atts['top_divider_height'] ) ) {
						$unit = preg_replace( '/[0-9.]/', '', $atts['top_divider_height'] );
						if ( ! $unit ) {
							$atts['top_divider_height'] .= 'px';
						}
					}

					$output .= '<div class="' . implode( ' ', $top_divider_attr ) . '" style="' . ( empty( $atts['top_divider_color'] ) ? '' : 'fill:' . $atts['top_divider_color'] . ';'  ) . ( empty( $atts['top_divider_height'] ) ? '' : 'height:' . $atts['top_divider_height'] . ';'  ) .'">';
					if( 'custom' == $atts['top_divider_type'] ) {
						$output .= $atts['top_divider_custom'];
					} else {
						$output .= porto_sh_commons( 'shape_divider' )[$atts['top_divider_type']];
					}
					$output .= '</div>';
				}

				if ( $atts['add_container'] ) {
					$output .= '<div class="container">';
				}
					$output .= do_shortcode( $content );
				if ( $atts['add_container'] ) {
					$output .= '</div>';
				}


				if( ! empty( $atts['bottom_divider_type'] ) ) {
					$bottom_divider_attr = array(
						'shape-divider shape-divider-bottom',
					);
					if( ! empty( $atts['bottom_divider_class'] ) ) {
						$bottom_divider_attr[] = $atts['bottom_divider_class'];
					}
					if( ! empty( $atts['bottom_divider_invert'] ) && ! empty( $atts['bottom_divider_flip'] ) ) {
						$bottom_divider_attr[] = 'shape-divider-reverse-xy';		
					} elseif( ! empty( $atts['bottom_divider_invert'] ) ) {
						$bottom_divider_attr[] = 'shape-divider-reverse-x';
					} elseif( ! empty( $atts['bottom_divider_flip'] ) ) {
						$bottom_divider_attr[] = 'shape-divider-reverse-y';
					}
					if ( ! empty( $atts['bottom_divider_height'] ) ) {
						$unit = preg_replace( '/[0-9.]/', '', $atts['bottom_divider_height'] );
						if ( ! $unit ) {
							$atts['bottom_divider_height'] .= 'px';
						}
					}

					$output .= '<div class="' . implode( ' ', $bottom_divider_attr ) . '" style="' . ( empty( $atts['bottom_divider_color'] ) ? '' : 'fill:' . $atts['bottom_divider_color'] . ';'  ) . ( empty( $atts['bottom_divider_height'] ) ? '' : 'height:' . $atts['bottom_divider_height'] . ';'  ) .'">';
					if( 'custom' == $atts['bottom_divider_type'] ) {
						$output .= $atts['bottom_divider_custom'];
					} else {
						$output .= porto_sh_commons( 'shape_divider' )[$atts['bottom_divider_type']];
					}
					$output .= '</div>';
				}

				$output .= '</' . esc_html( $atts['tag'] ) . '>';

				return $output;
			},
		)
	);
}

// Porto Section
add_action( 'vc_after_init', 'porto_load_section_shortcode' );

function porto_load_section_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'            => 'Porto ' . __( 'Section', 'porto-functionality' ),
			'base'            => 'porto_section',
			'is_container' => true,
			'category'        => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Group multiple rows in porto section.', 'porto-functionality' ),
			'icon'            => 'far fa-file',
			'show_settings_on_create' => false,
			'as_parent'       => array(
				'only' => 'vc_row',
			),
			'as_child' => array(
				'only' => '', // Only root
			),
			'js_view'         => 'VcSectionView',
			'params'          => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Anchor Name', 'porto-functionality' ),
					'param_name'  => 'anchor',
					'admin_label' => true,
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Wrap as Container', 'porto-functionality' ),
					'param_name' => 'container',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Section & Parallax Text Color', 'porto-functionality' ),
					'param_name' => 'section_text_color',
					'value'      => porto_sh_commons( 'section_text_color' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Text Align', 'porto-functionality' ),
					'param_name' => 'text_align',
					'value'      => porto_sh_commons( 'align' ),
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Is Section?', 'porto-functionality' ),
					'param_name'  => 'is_section',
					'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'admin_label' => true,
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Section Skin Color', 'porto-functionality' ),
					'param_name' => 'section_skin',
					'value'      => porto_sh_commons( 'section_skin' ),
					'dependency' => array(
						'element'   => 'is_section',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Section Default Color Scale', 'porto-functionality' ),
					'param_name' => 'section_color_scale',
					'value'      => porto_sh_commons( 'section_color_scale' ),
					'dependency' => array(
						'element' => 'section_skin',
						'value'   => array( 'default' ),
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Section Color Scale', 'porto-functionality' ),
					'param_name' => 'section_skin_scale',
					'dependency' => array(
						'element' => 'section_skin',
						'value'   => array( 'primary', 'secondary', 'tertiary', 'quaternary', 'dark', 'light' ),
					),
					'value'      => array(
						__( 'Default', 'porto-functionality' ) => '',
						__( 'Scale 2', 'porto-functionality' ) => 'scale-2',
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Remove Margin Top', 'porto-functionality' ),
					'param_name' => 'remove_margin_top',
					'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'dependency' => array(
						'element'   => 'is_section',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Remove Margin Bottom', 'porto-functionality' ),
					'param_name' => 'remove_margin_bottom',
					'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'dependency' => array(
						'element'   => 'is_section',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Remove Padding Top', 'porto-functionality' ),
					'param_name' => 'remove_padding_top',
					'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'dependency' => array(
						'element'   => 'is_section',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Remove Padding Bottom', 'porto-functionality' ),
					'param_name' => 'remove_padding_bottom',
					'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'dependency' => array(
						'element'   => 'is_section',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Remove Border', 'porto-functionality' ),
					'param_name' => 'remove_border',
					'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'dependency' => array(
						'element'   => 'is_section',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Divider', 'porto-functionality' ),
					'param_name' => 'show_divider',
					'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'dependency' => array(
						'element'   => 'is_section',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Divider Position', 'porto-functionality' ),
					'param_name' => 'divider_pos',
					'value'      => array(
						__( 'Top', 'porto-functionality' ) => '',
						__( 'Bottom', 'porto-functionality' ) => 'bottom',
					),
					'dependency' => array(
						'element'   => 'show_divider',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Divider Color', 'porto-functionality' ),
					'param_name' => 'divider_color',
					'dependency' => array(
						'element'   => 'show_divider',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Divider Height', 'porto-functionality' ),
					'param_name' => 'divider_height',
					'dependency' => array(
						'element'   => 'show_divider',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Divider Icon', 'porto-functionality' ),
					'param_name' => 'show_divider_icon',
					'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'dependency' => array(
						'element'   => 'show_divider',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Icon library', 'js_composer' ),
					'value'      => array(
						__( 'Font Awesome', 'porto-functionality' ) => 'fontawesome',
						__( 'Simple Line Icon', 'porto-functionality' ) => 'simpleline',
						__( 'Custom Image Icon', 'porto-functionality' ) => 'image',
					),
					'param_name' => 'divider_icon_type',
					'dependency' => array(
						'element'   => 'show_divider_icon',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'attach_image',
					'heading'    => __( 'Select Icon', 'porto-functionality' ),
					'dependency' => array(
						'element' => 'divider_icon_type',
						'value'   => 'image',
					),
					'param_name' => 'divider_icon_image',
				),
				array(
					'type'       => 'iconpicker',
					'heading'    => __( 'Select Icon', 'porto-functionality' ),
					'param_name' => 'divider_icon',
					'dependency' => array(
						'element' => 'divider_icon_type',
						'value'   => 'fontawesome',
					),
				),
				array(
					'type'       => 'iconpicker',
					'heading'    => __( 'Select Icon', 'porto-functionality' ),
					'param_name' => 'divider_icon_simpleline',
					'value'      => '',
					'settings'   => array(
						'type'         => 'simpleline',
						'iconsPerPage' => 4000,
					),
					'dependency' => array(
						'element' => 'divider_icon_type',
						'value'   => 'simpleline',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Icon Skin Color', 'porto-functionality' ),
					'param_name' => 'divider_icon_skin',
					'std'        => 'custom',
					'value'      => porto_sh_commons( 'colors' ),
					'dependency' => array(
						'element'   => 'show_divider_icon',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Icon Color', 'porto-functionality' ),
					'param_name' => 'divider_icon_color',
					'dependency' => array(
						'element' => 'divider_icon_skin',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Icon Background Color', 'porto-functionality' ),
					'param_name' => 'divider_icon_bg_color',
					'dependency' => array(
						'element' => 'divider_icon_skin',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Icon Border Color', 'porto-functionality' ),
					'param_name' => 'divider_icon_border_color',
					'dependency' => array(
						'element' => 'divider_icon_skin',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Icon Wrap Border Color', 'porto-functionality' ),
					'param_name' => 'divider_icon_wrap_border_color',
					'dependency' => array(
						'element' => 'divider_icon_skin',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Icon Style', 'porto-functionality' ),
					'param_name' => 'divider_icon_style',
					'value'      => porto_sh_commons( 'separator_icon_style' ),
					'dependency' => array(
						'element'   => 'show_divider_icon',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Icon Position', 'porto-functionality' ),
					'param_name' => 'divider_icon_pos',
					'value'      => porto_sh_commons( 'separator_icon_pos' ),
					'dependency' => array(
						'element'   => 'show_divider_icon',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Icon Size', 'porto-functionality' ),
					'param_name' => 'divider_icon_size',
					'value'      => porto_sh_commons( 'separator_icon_size' ),
					'dependency' => array(
						'element'   => 'show_divider_icon',
						'not_empty' => true,
					),
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Section' ) ) {
		class WPBakeryShortCode_Porto_Section extends WPBakeryShortCodesContainer {
		}
	}
}
