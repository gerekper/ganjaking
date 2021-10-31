<?php

if ( ! function_exists( 'porto_ultimate_heading_spacer' ) ) {
	function porto_ultimate_heading_spacer( $wrapper_class, $wrapper_style, $icon_inline ) {
		$spacer = '<div class="porto-u-heading-spacer ' . $wrapper_class . '" style="' . $wrapper_style . '">' . $icon_inline . '</div>';
		return $spacer;
	}
}
	$wrapper_style = $main_heading_style_inline = $sub_heading_style_inline = $line_style_inline = $icon_inline = $output = $el_class = $animation_type = '';
	extract(
		shortcode_atts(
			array(
				'main_heading'                 => '',
				'main_heading_use_theme_fonts' => '',
				'main_heading_font'            => '',
				'main_heading_font_family'     => '',
				'main_heading_font_size'       => '',
				'main_heading_font_weight'     => '',
				'main_heading_text_transform'  => '',
				'main_heading_line_height'     => '',
				'main_heading_letter_spacing'  => '',
				'main_heading_color'           => '',
				'main_heading_margin_bottom'   => '',
				'sub_heading_font'             => '',
				'sub_heading_font_family'      => '',
				'sub_heading_font_size'        => '',
				'sub_heading_font_weight'      => '',
				'sub_heading_line_height'      => '',
				'sub_heading_letter_spacing'   => '',
				'sub_heading_color'            => '',
				'sub_heading_margin_bottom'    => '',
				'spacer'                       => 'no_spacer',
				'spacer_position'              => 'top',
				'line_style'                   => 'solid',
				'line_width'                   => 'auto',
				'line_height'                  => '1',
				'line_color'                   => '#ccc',
				'alignment'                    => 'center',
				'spacer_margin_bottom'         => '',
				'enable_typewriter'            => false,
				'typewriter_animation'         => 'fadeIn',
				'typewriter_delay'             => 0,
				'typewriter_width'             => 0,
				'heading_tag'                  => '',
				'animation_type'               => '',
				'animation_duration'           => '',
				'animation_delay'              => '',
				'el_class'                     => '',
				'className'                    => '',
			),
			$atts
		)
	);
	$wrapper_class = $spacer;

	if ( $className ) {
		if ( $el_class ) {
			$el_class .= ' ' . $className;
		} else {
			$el_class = $className;
		}
	}

	if ( ! empty( $shortcode_class ) ) {
		if ( empty( $el_class ) ) {
			$el_class = $shortcode_class;
		} else {
			$el_class .= ' ' . $shortcode_class;
		}
	}
	$type_plugin = '';
	if ( ! empty( $enable_typewriter ) ) {
		$typewriter_options = array(
			'startDelay'     => 0,
			'minWindowWidth' => 0,
		);
		if ( ! empty( $typewriter_delay ) ) {
			$typewriter_options['startDelay'] = (int) $typewriter_delay;
		}
		if ( ! empty( $typewriter_width ) ) {
			$typewriter_options['minWindowWidth'] = (int) $typewriter_width;
		}
		if ( ! empty( $typewriter_animation ) ) {
			$typewriter_options['animationName'] = $typewriter_animation;
		}
		$type_plugin .= ' data-plugin-animated-letters data-plugin-options="' . esc_attr( json_encode( $typewriter_options ) ) . '"';
	}
	if ( is_array( $line_height ) && isset( $line_height['size'] ) ) {
		$line_height = $line_height['size'];
	}

	if ( empty( $content ) && isset( $atts['content'] ) && ! empty( $atts['content'] ) ) {
		$content = $atts['content'];
	}

	if ( '' == $heading_tag ) {
		$heading_tag = 'h2';
	}
	if ( empty( $atts['main_heading_porto_typography'] ) ) {
		if ( ( ! isset( $atts['main_heading_use_theme_fonts'] ) || 'yes' !== $atts['main_heading_use_theme_fonts'] ) && $main_heading_font ) {
			$google_fonts_data          = porto_sc_parse_google_font( $main_heading_font );
			$styles                     = porto_sc_google_font_styles( $google_fonts_data );
			$main_heading_style_inline .= esc_attr( $styles );
		} elseif ( $main_heading_font_family ) {
			$main_heading_style_inline .= 'font-family:' . esc_attr( $main_heading_font_family ) . ';';
		}
		if ( $main_heading_font_weight ) {
			$main_heading_style_inline .= 'font-weight:' . esc_attr( $main_heading_font_weight ) . ';';
		}
	}
	if ( $main_heading_color ) {
		$main_heading_style_inline .= 'color:' . esc_attr( $main_heading_color ) . ';';
	}
	if ( $main_heading_margin_bottom || '0' == $main_heading_margin_bottom ) {
		$unit = trim( preg_replace( '/[0-9.-]/', '', $main_heading_margin_bottom ) );
		if ( ! $unit ) {
			$main_heading_margin_bottom .= 'px';
		}
		$main_heading_style_inline .= 'margin-bottom: ' . esc_attr( $main_heading_margin_bottom ) . ';';
	}

	if ( empty( $atts['sub_heading_porto_typography'] ) ) {
		if ( ( ! isset( $atts['sub_heading_use_theme_fonts'] ) || 'yes' !== $atts['sub_heading_use_theme_fonts'] ) && $sub_heading_font ) {
			$google_fonts_data1        = porto_sc_parse_google_font( $sub_heading_font );
			$styles                    = porto_sc_google_font_styles( $google_fonts_data1 );
			$sub_heading_style_inline .= esc_attr( $styles );
		} elseif ( $sub_heading_font_family ) {
			$sub_heading_style_inline .= 'font-family: ' . esc_attr( $sub_heading_font_family ) . ';';
		}
	}

	// enqueue google fonts
	$google_fonts_arr = array();
	if ( isset( $google_fonts_data ) && $google_fonts_data ) {
		$google_fonts_arr[] = $google_fonts_data;
	}
	if ( isset( $google_fonts_data1 ) && $google_fonts_data1 ) {
		$google_fonts_arr[] = $google_fonts_data1;
	}
	if ( ! empty( $google_fonts_arr ) ) {
		porto_sc_enqueue_google_fonts( $google_fonts_arr );
	}
	if ( empty( $atts['sub_heading_porto_typography'] ) ) {
		if ( $sub_heading_font_weight ) {
			$sub_heading_style_inline .= 'font-weight:' . esc_attr( $sub_heading_font_weight ) . ';';
		}
	}
	if ( $sub_heading_color ) {
		$sub_heading_style_inline .= 'color: ' . esc_attr( $sub_heading_color ) . ';';
	}
	if ( $sub_heading_margin_bottom || '0' == $sub_heading_margin_bottom ) {
		$unit = trim( preg_replace( '/[0-9.-]/', '', $sub_heading_margin_bottom ) );
		if ( ! $unit ) {
			$sub_heading_margin_bottom .= 'px';
		}
		$sub_heading_style_inline .= 'margin-bottom:' . esc_attr( $sub_heading_margin_bottom ) . ';';
	}

	if ( 'no_spacer' != $spacer && $spacer_margin_bottom ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $spacer_margin_bottom ) );
		if ( ! $unit ) {
			$spacer_margin_bottom .= 'px';
		}
		$wrapper_style .= 'margin-bottom: ' . esc_attr( $spacer_margin_bottom ) . ';';
	}
	if ( 'line_only' == $spacer ) {
		$wrap_width         = $line_width;
		$line_style_inline  = 'border-style:' . esc_attr( $line_style ) . ';';
		$line_style_inline .= 'border-bottom-width:' . esc_attr( $line_height ) . 'px;';
		$line_style_inline .= 'border-color:' . esc_attr( $line_color ) . ';';
		$line_style_inline .= 'width:' . esc_attr( $wrap_width ) . ( 'auto' == $wrap_width ? ';' : 'px;' );
		if ( 'center' != $alignment ) {
			$line_style_inline .= 'margin-' . esc_attr( $alignment ) . ': 0';
		}
		$wrapper_style .= 'height:' . esc_attr( $line_height ) . 'px;';
		$line           = '<span class="porto-u-headings-line" style="' . esc_attr( $line_style_inline ) . '"></span>';
		$icon_inline    = $line;
	} elseif ( 'image_only' == $spacer ) {
		if ( ! empty( $spacer_img_width ) ) {
			$siwidth = array( $spacer_img_width, $spacer_img_width );
		} else {
			$siwidth = 'full';
		}
		$spacer_inline = '';
		if ( $spacer_img ) {
			$attachment = wp_get_attachment_image_src( $spacer_img, $siwidth );
			if ( isset( $attachment ) ) {
				$icon_inline = $attachment[0];
			}
		}
		$alt = '';
		if ( '' !== $spacer_img_width ) {
			$spacer_inline = 'width:' . $spacer_img_width . 'px';
		}
		$icon_inline = '<img src="' . esc_url( $icon_inline ) . '" class="ultimate-headings-icon-image" alt="' . esc_attr( $alt ) . '" style="' . esc_attr( $spacer_inline ) . '"/>';
	}
	if ( empty( $atts['main_heading_porto_typography'] ) ) {
		if ( $main_heading_font_size ) {
			$unit = trim( preg_replace( '/[0-9.]/', '', $main_heading_font_size ) );
			if ( ! $unit ) {
				$main_heading_font_size .= 'px';
			}
			$main_heading_style_inline .= 'font-size:' . $main_heading_font_size . ';';
		}
		if ( $main_heading_text_transform ) {
			$main_heading_style_inline .= 'text-transform:' . $main_heading_text_transform . ';';
		}
		if ( $main_heading_line_height ) {
			$unit = trim( preg_replace( '/[0-9.]/', '', $main_heading_line_height ) );
			if ( ! $unit && (int) $main_heading_line_height > 3 ) {
				$main_heading_line_height .= 'px';
			}
			$main_heading_style_inline .= 'line-height:' . $main_heading_line_height . ';';
		}
		if ( $main_heading_letter_spacing ) {
			$main_heading_style_inline .= 'letter-spacing:' . $main_heading_letter_spacing . ';';
		}
	}
	if ( empty( $atts['sub_heading_porto_typography'] ) ) {
		if ( $sub_heading_font_size ) {
			$unit = trim( preg_replace( '/[0-9.]/', '', $sub_heading_font_size ) );
			if ( ! $unit ) {
				$sub_heading_font_size .= 'px';
			}
			$sub_heading_style_inline .= 'font-size:' . $sub_heading_font_size . ';';
		}
		if ( $sub_heading_line_height ) {
			$unit = trim( preg_replace( '/[0-9.]/', '', $sub_heading_line_height ) );
			if ( ! $unit && (int) $sub_heading_line_height > 3 ) {
				$sub_heading_line_height .= 'px';
			}
			$sub_heading_style_inline .= 'line-height:' . $sub_heading_line_height . ';';
		}
		if ( $sub_heading_letter_spacing ) {
			$sub_heading_style_inline .= 'letter-spacing:' . $sub_heading_letter_spacing . ';';
		}
	}

	if ( is_rtl() && 'left' === $alignment ) {
		$alignment = 'right';
	} elseif ( is_rtl() && 'right' === $alignment ) {
		$alignment = 'left';
	}

	$wrapper_attributes = array();
	if ( $animation_type ) {
		$wrapper_attributes[] = 'data-appear-animation="' . esc_attr( $animation_type ) . '"';
		if ( $animation_delay ) {
			$wrapper_attributes[] = 'data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
		}
		if ( $animation_duration && 1000 != $animation_duration ) {
			$wrapper_attributes[] = 'data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
		}
	}

	$output = '<div class="porto-u-heading' . ( $el_class ? ' ' . esc_attr( $el_class ) : '' ) . '" data-hspacer="' . esc_attr( $spacer ) . '" data-halign="' . esc_attr( $alignment ) . '" style="text-align:' . esc_attr( $alignment ) . '"' . ( $wrapper_attributes ? ' ' . implode( ' ', $wrapper_attributes ) : '' ) . porto_shortcode_add_floating_options( $atts ) . '>';
	if ( 'top' == $spacer_position && 'no_spacer' != $spacer ) {
		$output .= porto_ultimate_heading_spacer( $wrapper_class, $wrapper_style, $icon_inline );
	}
	if ( $main_heading ) {
		if ( ! isset( $main_heading_attrs_escaped ) ) {
			$main_heading_attrs_escaped = '';
		}
		if ( $main_heading_style_inline ) {
			$main_heading_attrs_escaped .= ' style="' . esc_attr( $main_heading_style_inline ) . '"';
		}
		$output .= '<div class="porto-u-main-heading"><' . esc_html( $heading_tag ) . ' ' . trim( $type_plugin ) . ' ' . $main_heading_attrs_escaped . '>' . wp_kses_post( $main_heading ) . '</' . esc_html( $heading_tag ) . '></div>';
	}
	if ( 'middle' == $spacer_position && 'no_spacer' != $spacer ) {
		$output .= porto_ultimate_heading_spacer( $wrapper_class, $wrapper_style, $icon_inline );
	}
	if ( isset( $content ) && $content ) {
		$output .= '<div class="porto-u-sub-heading" style="' . esc_attr( $sub_heading_style_inline ) . '">' . do_shortcode( $content ) . '</div>';
	}
	if ( 'bottom' == $spacer_position && 'no_spacer' != $spacer ) {
		$output .= porto_ultimate_heading_spacer( $wrapper_class, $wrapper_style, $icon_inline );
	}
	$output .= '</div>';

	echo porto_filter_output( $output );
