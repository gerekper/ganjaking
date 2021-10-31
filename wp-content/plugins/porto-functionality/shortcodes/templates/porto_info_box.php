<?php
if ( empty( $atts['icon_color_bg'] ) && ! isset( $atts['title_google_font_style_font_size'] ) ) {
	$atts['icon_color_bg'] = 'transparent';
}

$icon_type  = $icon_img = $img_width = $icon = $icon_color = $icon_color_bg = $icon_size = $icon_style = $icon_border_style = $icon_border_radius = $icon_color_border = $icon_border_size = $icon_border_spacing = $el_class = $animation_type = $title = $link = $hover_effect = $pos = $read_more = $read_text = $pos = $css_class = $desc_font_line_height = $title_font_line_height = $heading_tag = '';
$title_font = $title_font_style = $title_font_size = $title_font_color = $desc_font = $desc_font_style = $desc_font_size = $desc_font_color = $target = $link_title  = $rel = '';
extract(
	shortcode_atts(
		array(
			'icon_type'                    => 'fontawesome',
			'icon'                         => 'none',
			'icon_simpleline'              => 'none',
			'icon_porto'                   => 'none',
			'icon_img'                     => '',
			'img_width'                    => '48',
			'icon_size'                    => '32',
			'icon_color'                   => '',
			'icon_style'                   => 'none',
			'icon_color_bg'                => '#ffffff',
			'icon_color_border'            => '#333333',
			'icon_border_style'            => '',
			'icon_border_size'             => '1',
			'icon_border_radius'           => '500',
			'icon_border_spacing'          => '50',
			'icon_animation'               => '',
			'title'                        => '',
			'subtitle'                     => '',
			'link'                         => '',
			'hover_effect'                 => 'style_1',
			'pos'                          => 'default',
			'h_align'                      => 'center',
			'read_more'                    => 'none',
			'read_text'                    => 'Read More',
			'heading_tag'                  => 'h3',
			'title_use_theme_fonts'        => '',
			'title_google_font'            => '',
			'title_font'                   => '',
			'title_font_style'             => '',
			'title_font_size'              => '',
			'title_text_transform'         => '',
			'title_font_line_height'       => '',
			'title_font_letter_spacing'    => '',
			'title_font_color'             => '',
			'subtitle_font_style'          => '',
			'subtitle_font_size'           => '',
			'subtitle_font_line_height'    => '',
			'subtitle_font_letter_spacing' => '',
			'subtitle_font_color'          => '',
			'desc_use_theme_fonts'         => '',
			'desc_google_font'             => '',
			'desc_font'                    => '',
			'desc_font_style'              => '',
			'desc_font_size'               => '',
			'desc_font_color'              => '',
			'desc_font_line_height'        => '',
			'desc_font_letter_spacing'     => '',
			'el_class'                     => '',
			'css_info_box'                 => '',
			'animation_type'               => '',
			'className'                    => '',
			'icon_margin_bottom'           => '',
			'icon_margin_right'            => '',
			'title_margin_bottom'          => '',
			'sub_title_margin_bottom'      => '',
			'animation_type1'              => '',
			'animation_duration'           => '',
			'animation_delay'              => '',
		),
		$atts,
		'porto_info_box'
	)
);

if ( ( ! isset( $content ) || empty( $content ) ) && isset( $atts['content'] ) ) {
	$content = $atts['content'];
}
if ( ! $icon ) {
	$icon = 'none';
}
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

switch ( $icon_type ) {
	case 'simpleline':
		$icon = $icon_simpleline;
		break;
	case 'porto':
		$icon = $icon_porto;
		break;
}

$html = $target = $title_style = $desc_style = $inf_design_style = '';
if ( defined( 'VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG' ) ) {
	$inf_design_style = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css_info_box, ' ' ), 'porto_info_box', $atts );
}

$icon_margin_left = '';
if ( 'heading-right' == $pos || 'right' == $pos ) {
	$icon_margin_left  = $icon_margin_right;
	$icon_margin_right = '';
}
$box_icon = do_shortcode( '[porto_icon icon_type="' . $icon_type . '" icon="' . trim( $icon ) . '" icon_img="' . $icon_img . '" img_width="' . $img_width . '" icon_size="' . $icon_size . '" icon_color="' . $icon_color . '" icon_style="' . $icon_style . '" icon_color_bg="' . $icon_color_bg . '" icon_color_border="' . $icon_color_border . '"  icon_border_style="' . $icon_border_style . '" icon_border_size="' . $icon_border_size . '" icon_border_radius="' . $icon_border_radius . '" icon_border_spacing="' . $icon_border_spacing . '" animation_type="' . $animation_type . '" icon_margin_bottom="' . $icon_margin_bottom . '" icon_margin_left="' . $icon_margin_left . '" icon_margin_right="' . $icon_margin_right . '"' . ( 'top' == $pos && $h_align && 'center' != $h_align ? ' icon_align="' . $h_align . '"' : '' ) . ']' );
$classes  = 'porto-sicon-box';
if ( $inf_design_style ) {
	$classes .= ' ' . $inf_design_style;
}
if ( $css_class ) {
	$classes .= ' ' . $css_class;
}
if ( $el_class ) {
	$classes .= ' ' . $el_class;
}
if ( $hover_effect ) {
	$classes .= ' ' . $hover_effect;
}

$ic_class = '';
if ( $pos ) {
	$classes .= ' ' . $pos . '-icon';
	$ic_class = 'porto-sicon-' . $pos;
	if ( 'default' == $pos && $content ) {
		$classes .= ' flex-wrap';
	}
	if ( 'top' == $pos && $h_align && 'center' != $h_align ) {
		if ( 'left' == $h_align ) {
			$h_align = 'start';
		} elseif ( 'right' == $h_align ) {
			$h_align = 'end';
		}
		$classes .= ' text-' . $h_align;
	}
}

if ( $link ) {
	if ( is_array( $link ) && isset( $link['url'] ) ) {
		$url        = $link['url'];
		$target     = isset( $link['is_external'] ) && 'on' == $link['is_external'] ? ' target="_blank"' : '';
		$link_title = '';
		$rel        = isset( $link['nofollow'] ) && 'on' == $link['nofollow'] ? ' rel="nofollow' . ( $target ? ' noopener noreferrer' : '' ) . '"' : ( $target ? ' rel="noopener noreferrer"' : '' );
	} elseif ( defined( 'VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG' ) ) {
		$href       = vc_build_link( $link );
		$url        = ( isset( $href['url'] ) && $href['url'] ) ? $href['url'] : '';
		$target     = ( isset( $href['target'] ) && $href['target'] ) ? " target='" . esc_attr( trim( $href['target'] ) ) . "'" : '';
		$link_title = ( isset( $href['title'] ) && $href['title'] ) ? " title='" . esc_attr( $href['title'] ) . "'" : '';
		$rel        = ( isset( $href['rel'] ) && $href['rel'] ) ? " rel='" . esc_attr( $href['rel'] ) . "'" : '';
	} elseif ( is_string( $link ) ) {
		$url = $link;
	}
}

/* title */
if ( empty( $atts['title_font_porto_typography'] ) ) {
	if ( ( ! isset( $atts['title_use_theme_fonts'] ) || 'yes' !== $atts['title_use_theme_fonts'] ) && $title_google_font ) {
		$google_fonts_data = porto_sc_parse_google_font( $title_google_font );
		$styles            = porto_sc_google_font_styles( $google_fonts_data );
		$title_style      .= esc_attr( $styles );
	} elseif ( $title_font ) {
		$title_style .= 'font-family:\'' . esc_attr( $title_font ) . '\';';
	}
	if ( $title_font_style ) {
		$title_style .= 'font-weight:' . esc_attr( $title_font_style ) . ';';
	}
	if ( $title_font_size ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $title_font_size ) );
		if ( ! $unit ) {
			$title_font_size .= 'px';
		}
		$title_style .= 'font-size:' . esc_attr( $title_font_size ) . ';';
	}
	if ( $title_text_transform ) {
		$title_style .= 'text-transform:' . esc_attr( $title_text_transform ) . ';';
	}
	if ( $title_font_line_height ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $title_font_line_height ) );
		if ( ! $unit && (int) $title_font_line_height > 3 ) {
			$title_font_line_height .= 'px';
		}
		$title_style .= 'line-height:' . esc_attr( $title_font_line_height ) . ';';
	}
	if ( $title_font_letter_spacing || '0' == $title_font_letter_spacing ) {
		$title_style .= 'letter-spacing:' . esc_attr( $title_font_letter_spacing ) . ';';
	}
}
if ( $title_font_color ) {
	$title_style .= 'color:' . esc_attr( $title_font_color ) . ';';
}
if ( $title_margin_bottom && is_string( $title_margin_bottom ) ) {
	$unit = trim( preg_replace( '/[0-9.-]/', '', $title_margin_bottom ) );
	if ( ! $unit ) {
		$title_margin_bottom .= 'px';
	}
	$title_style .= 'margin-bottom:' . esc_attr( $title_margin_bottom ) . ';';
}

/* sub title */
$subtitle_style = '';
if ( empty( $atts['subtitle_font_porto_typography'] ) ) {
	if ( $subtitle_font_style ) {
		$subtitle_style .= 'font-weight:' . esc_attr( $subtitle_font_style ) . ';';
	}
	if ( $subtitle_font_size ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $subtitle_font_size ) );
		if ( ! $unit ) {
			$subtitle_font_size .= 'px';
		}
		$subtitle_style .= 'font-size:' . esc_attr( $subtitle_font_size ) . ';';
	}
	if ( $subtitle_font_line_height ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $subtitle_font_line_height ) );
		if ( ! $unit && (int) $subtitle_font_line_height > 3 ) {
			$subtitle_font_line_height .= 'px';
		}
		$subtitle_style .= 'line-height:' . esc_attr( $subtitle_font_line_height ) . ';';
	}
	if ( $subtitle_font_letter_spacing ) {
		$subtitle_style .= 'letter-spacing:' . esc_attr( $subtitle_font_letter_spacing ) . ';';
	}
}
if ( $subtitle_font_color ) {
	$subtitle_style .= 'color:' . esc_attr( $subtitle_font_color ) . ';';
}

/* description */
if ( empty( $atts['desc_font_porto_typography'] ) ) {
	if ( ( ! isset( $atts['desc_use_theme_fonts'] ) || 'yes' !== $atts['desc_use_theme_fonts'] ) && $desc_google_font ) {
		$google_fonts_data1 = porto_sc_parse_google_font( $desc_google_font );
		$styles             = porto_sc_google_font_styles( $google_fonts_data1 );
		$desc_style        .= $styles;
	} elseif ( $desc_font ) {
		$desc_style .= 'font-family:\'' . $desc_font . '\';';
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
if ( empty( $atts['desc_font_porto_typography'] ) ) {
	if ( $desc_font_style ) {
		$desc_style .= 'font-weight:' . $desc_font_style . ';';
	}

	if ( $desc_font_size ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $desc_font_size ) );
		if ( ! $unit ) {
			$desc_font_size .= 'px';
		}
		$desc_style .= 'font-size:' . $desc_font_size . ';';
	}
	if ( $desc_font_line_height ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $desc_font_line_height ) );
		if ( ! $unit && (int) $desc_font_line_height > 3 ) {
			$desc_font_line_height .= 'px';
		}
		$desc_style .= 'line-height:' . $desc_font_line_height . ';';
	}
	if ( $desc_font_letter_spacing ) {
		$desc_style .= 'letter-spacing:' . $desc_font_letter_spacing . ';';
	}
}

if ( $desc_font_color ) {
	$desc_style .= 'color:' . $desc_font_color . ';';
}

if ( ! isset( $title_attrs_escaped ) ) {
	$title_attrs_escaped = ' class="porto-sicon-title"';
}
if ( ! isset( $subtitle_attrs_escaped ) ) {
	$subtitle_attrs_escaped = '';
}
if ( ! isset( $desc_attrs_escaped ) ) {
	$desc_attrs_escaped = '';
}

$header_style = '';
if ( $sub_title_margin_bottom ) {
	$unit = trim( preg_replace( '/[0-9.-]/', '', $sub_title_margin_bottom ) );
	if ( ! $unit ) {
		$sub_title_margin_bottom .= 'px';
	}
	$header_style .= 'margin-bottom:' . esc_attr( $sub_title_margin_bottom ) . ';';
}

$wrapper_attributes = '';
if ( $animation_type1 ) {
	$wrapper_attributes .= ' data-appear-animation="' . esc_attr( $animation_type1 ) . '"';
	if ( $animation_delay ) {
		$wrapper_attributes .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$wrapper_attributes .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
}

$html .= '<div class="' . esc_attr( $classes ) . '"' . $wrapper_attributes . '>';

if ( 'heading-right' == $pos || 'right' == $pos ) {
	if ( 'right' == $pos ) {
		$html .= '<div class="porto-sicon-body" >';
	}
	if ( $title || $subtitle ) {
		$html       .= '<div class="porto-sicon-header"' . ( $header_style ? ' style="' . esc_attr( $header_style ) . '"' : '' ) . '>';
		$link_prefix = '';
		$link_sufix  = '';
		if ( $link ) {
			if ( 'title' == $read_more ) {
				$link_prefix = '<a class="porto-sicon-box-link" href="' . esc_url( $url ) . '"' . $target . $rel . $link_title . '>';
				$link_sufix  = '</a>';
			}
		}
		if ( $title ) {
			$html .= $link_prefix . '<' . esc_attr( $heading_tag ) . $title_attrs_escaped . ( $title_style ? ' style="' . esc_attr( $title_style ) . '"' : '' ) . '>' . porto_strip_script_tags( $title ) . '</' . esc_attr( $heading_tag ) . '>' . $link_sufix;
		}
		if ( $subtitle ) {
			$html .= '<p' . $subtitle_attrs_escaped . ( $subtitle_style ? ' style="' . $subtitle_style . '"' : '' ) . '>' . porto_strip_script_tags( $subtitle ) . '</p>';
		}
		$html .= '</div> <!-- header -->';
	}
	if ( 'right' !== $pos ) {
		if ( 'none' !== $icon || $icon_img ) {
			$html .= '<div class="' . esc_attr( $ic_class ) . '" >' . $box_icon . '</div>';
		}
	}
	if ( $content ) {
		$html .= '<div class="porto-sicon-description" style="' . esc_attr( $desc_style ) . '"' . $desc_attrs_escaped . '>';
		$html .= do_shortcode( $content );
		if ( $link ) {
			if ( 'more' == $read_more ) {
				$more_link  = '<a class="porto-sicon-read" href="' . esc_url( $url ) . '"' . $target . $rel . $link_title . '>';
				$more_link .= $read_text;
				$more_link .= '&nbsp;&raquo;';
				$more_link .= '</a>';
				$html      .= $more_link;
			}
		}
		$html .= '</div>';
	}
	if ( 'right' == $pos ) {
		$html .= '</div>';
		if ( 'none' !== $icon || $icon_img ) {
			$html .= '<div class="' . esc_attr( $ic_class ) . '">' . $box_icon . '</div>';
		}
	}
} else {
	if ( 'none' !== $icon || $icon_img ) {
		$html .= '<div class="' . esc_attr( $ic_class ) . '">' . $box_icon . '</div>';
	}
	if ( 'left' == $pos ) {
		$html .= '<div class="porto-sicon-body">';
	}
	if ( $title || $subtitle ) {
		$html       .= '<div class="porto-sicon-header"' . ( $header_style ? ' style="' . esc_attr( $header_style ) . '"' : '' ) . '>';
		$link_prefix = '';
		$link_sufix  = '';
		if ( $link ) {
			if ( 'title' == $read_more ) {
				$link_prefix = '<a class="porto-sicon-box-link" href="' . esc_url( $url ) . '"' . $target . $rel . $link_title . '>';
				$link_sufix  = '</a>';
			}
		}
		if ( $title ) {
			$html .= $link_prefix . '<' . esc_attr( $heading_tag ) . $title_attrs_escaped . ' style="' . esc_attr( $title_style ) . '">' . porto_strip_script_tags( $title ) . '</' . esc_attr( $heading_tag ) . '>' . $link_sufix;
		}
		if ( $subtitle ) {
			$html .= '<p' . $subtitle_attrs_escaped . ( $subtitle_style ? ' style="' . $subtitle_style . '"' : '' ) . '>' . porto_strip_script_tags( $subtitle ) . '</p>';
		}
		$html .= '</div> <!-- header -->';
	}
	if ( $content || ( $link && 'more' == $read_more && $read_text ) ) {
		$html .= '<div class="porto-sicon-description" style="' . esc_attr( $desc_style ) . '"' . $desc_attrs_escaped . '>';
		$html .= do_shortcode( $content );
		if ( $link && 'more' == $read_more && $read_text ) {
			$more_link  = '<a class="porto-sicon-read xx" href="' . esc_url( $url ) . '"' . $target . $rel . $link_title . '>';
			$more_link .= $read_text;
			$more_link .= '<span>&nbsp;&raquo;</span>';
			$more_link .= '</a>';
			$html      .= $more_link;
		}
		$html .= '</div> <!-- description -->';
	}
	if ( 'left' == $pos ) {
		$html .= '</div>';
	}
}


$html .= '</div><!-- porto-sicon-box -->';
if ( $link ) {
	if ( 'box' == $read_more ) {
		$html = '<a class="porto-sicon-box-link" href="' . esc_url( $url ) . '"' . $target . $rel . $link_title . '>' . $html . '</a>';
	}
}

echo porto_filter_output( $html );
