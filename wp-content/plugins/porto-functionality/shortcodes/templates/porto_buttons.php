<?php


$output             = $btn_title = $btn_link = $btn_size = $btn_width = $btn_height = $btn_hover = $btn_bg_color = $btn_radius = '';
$btn_bg_color_hover = $btn_border_style = $btn_color_border = $btn_border_size = $el_class = '';
$btn_font_family    = $btn_font_style = $btn_title_color = $btn_font_size = '';
$btn_padding_left   = $btn_padding_top = $btn_title_color_hover = $btn_align = $btn_color_border_hover = '';
$rel                = $btn_line_height = $target = $link_title = '';
extract(
	shortcode_atts(
		array(
			'btn_title'                => '',
			'btn_link'                 => '',
			'btn_size'                 => 'porto-btn-normal',
			'btn_width'                => '',
			'btn_height'               => '',
			'btn_padding_left'         => '',
			'btn_padding_top'          => '',
			'btn_hover'                => 'porto-btn-no-hover-bg',
			'btn_bg_color'             => '#e0e0e0',
			'btn_radius'               => '',
			'btn_bg_color_hover'       => '',
			'btn_title_color_hover'    => '',
			'btn_border_style'         => '',
			'btn_color_border'         => '',
			'btn_color_border_hover'   => '',
			'btn_border_size'          => '',
			'btn_font_use_theme_fonts' => '',
			'btn_font'                 => '',
			'btn_font_family'          => '',
			'btn_font_style'           => '',
			'btn_title_color'          => '#000000',
			'btn_font_size'            => '',
			'btn_line_height'          => '',
			'btn_align'                => 'porto-btn-left',
			'rel'                      => '',
			'el_class'                 => '',
			'css_adv_btn'              => '',
			'animation_type'           => '',
			'animation_delay'          => '',
			'animation_duration'       => '',
		),
		$atts
	)
);
$style            = $hover_style = $btn_style_inline = $link_attrs = $shadow_click = $shadow_color = $box_shadow = $main_extra_class = '';
$main_extra_class = $el_class;
$el_class         = $css_btn_design = '';
$el_class        .= ' ';
$uniqid           = uniqid();

if ( ! empty( $shortcode_class ) ) {
	$el_class .= $shortcode_class . ' ';
}

$vc_version    = ( defined( 'WPB_VC_VERSION' ) ) ? WPB_VC_VERSION : 0;
$is_vc_49_plus = ( version_compare( 4.9, $vc_version, '<=' ) ) ? 'porto-adjust-bottom-margin' : '';

if ( defined( 'VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG' ) ) {
	$css_btn_design = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css_adv_btn, ' ' ), 'porto_buttons', $atts );
}

	$shadow_click = 'none';
$alt              = 'icon';
if ( $btn_link && function_exists( 'vc_build_link' ) ) {
	$href = vc_build_link( $btn_link );
	if ( isset( $href['url'] ) && $href['url'] ) {
		$url        = ( isset( $href['url'] ) && $href['url'] ) ? $href['url'] : '';
		$target     = ( isset( $href['target'] ) && $href['target'] ) ? "target='" . esc_attr( trim( $href['target'] ) ) . "'" : '';
		$link_title = ( isset( $href['title'] ) && $href['title'] ) ? "title='" . esc_attr( $href['title'] ) . "'" : '';
		$rel        = ( isset( $href['rel'] ) && $href['rel'] ) ? "rel='" . esc_attr( $rel ) . ' ' . esc_attr( $href['rel'] ) . "'" : "rel='" . esc_attr( $rel ) . "'";

		$link_attrs .= ' ' . $link_title . ' ' . $rel . ' href = "' . esc_url( $url ) . '" ' . $target;
	}
}
if ( empty( $atts['btn_porto_typography'] ) ) {
	if ( ( ! isset( $atts['btn_font_use_theme_fonts'] ) || 'yes' !== $atts['btn_font_use_theme_fonts'] ) && $btn_font ) {
		$google_fonts_data = porto_sc_parse_google_font( $btn_font );
		$styles            = porto_sc_google_font_styles( $google_fonts_data );
		$btn_style_inline .= esc_attr( $styles );
		porto_sc_enqueue_google_fonts( array( $google_fonts_data ) );
	} elseif ( $btn_font_family ) {
		$btn_style_inline .= 'font-family:\'' . esc_attr( $btn_font_family ) . '\';';
	}
	if ( $btn_font_style ) {
		$btn_style_inline .= 'font-weight:' . esc_attr( $btn_font_style ) . ';';
	}

	if ( $btn_font_size ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $btn_font_size ) );
		if ( ! $unit ) {
			$btn_font_size .= 'px';
		}
		$btn_style_inline .= 'font-size:' . esc_attr( $btn_font_size ) . ';';
	}
	if ( $btn_line_height && ( 'porto-btn-custom' != $btn_size || ! $btn_height ) ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $btn_line_height ) );
		if ( ! $unit && (int) $btn_line_height > 3 ) {
			$btn_line_height .= 'px';
		}
		$btn_style_inline .= 'line-height:' . esc_attr( $btn_line_height ) . ';';
	}
}

$style .= $btn_style_inline;
if ( 'porto-btn-custom' == $btn_size ) {
	if ( $btn_width ) {
		$style .= 'width:' . esc_attr( $btn_width ) . 'px;';
	}
	if ( $btn_height ) {
		$style .= 'min-height:' . esc_attr( $btn_height ) . 'px;';
		if ( ! $btn_padding_top ) {
			$style .= 'line-height:' . ( esc_attr( $btn_height ) - 1 ) . 'px;';
		}
	}
	if ( $btn_padding_top && $btn_padding_left ) {
		$style .= 'padding:' . esc_attr( $btn_padding_top ) . 'px ' . esc_attr( $btn_padding_left ) . 'px;';
	}
}
if ( $btn_border_style ) {
	if ( $btn_radius ) {
		$style .= 'border-radius:' . esc_attr( $btn_radius ) . 'px;';
	}
	if ( $btn_border_size ) {
		$style .= 'border-width:' . esc_attr( $btn_border_size ) . 'px;';
	}
	if ( $btn_color_border ) {
		$style .= 'border-color:' . esc_attr( $btn_color_border ) . ';';
	}
	$style .= 'border-style:' . esc_attr( $btn_border_style ) . ';';
} else {
	$style .= 'border:none;';
}

if ( $btn_bg_color ) {
	$style .= 'background: ' . esc_attr( $btn_bg_color ) . ';';
}
if ( $btn_title_color ) {
	$style .= 'color: ' . esc_attr( $btn_title_color ) . ';';
}

if ( $btn_align ) {
	$el_class .= ' ' . esc_attr( $btn_align ) . ' ';
}

$el_class .= $main_extra_class;

$output .= '<a class="porto-btn' . ( $css_btn_design ? ' ' . esc_attr( $css_btn_design ) : '' ) . ' ' . esc_attr( $is_vc_49_plus ) . ' ' . esc_attr( $btn_size ) . ' ' . esc_attr( $btn_hover ) . esc_attr( $el_class ) . '"' . $link_attrs . ' data-hover="' . esc_attr( $btn_title_color_hover ) . '" data-border-color="' . esc_attr( $btn_color_border ) . '" data-bg="' . esc_attr( $btn_bg_color ) . '" data-hover-bg="' . esc_attr( $btn_bg_color_hover ) . '" data-border-hover="' . esc_attr( $btn_color_border_hover ) . '" data-shadow-click="' . esc_attr( $shadow_click ) . '" data-shadow="' . esc_attr( $box_shadow ) . '" style="' . esc_attr( $style ) . '">';

$output .= '<span class="porto-btn-hover"' . ( $btn_bg_color_hover ? ' style="background-color:' . esc_attr( $btn_bg_color_hover ) . '"' : '' ) . '></span>';
$output .= '<span class="porto-btn-data porto-btn-text " >' . esc_html( $btn_title ) . '</span>';
$output .= '</a>';

//  Add a wrapper class to handle bottom margin
$wrapper_class = '';
switch ( $btn_align ) {
	case 'porto-btn-inline':
		$wrapper_class = 'porto-btn-ctn-inline';
		break;
	case 'porto-btn-center':
		$wrapper_class = 'porto-btn-ctn-center';
		break;
	case 'porto-btn-right':
		$wrapper_class = 'porto-btn-ctn-right';
		break;
	case 'porto-btn-left':
	default:
		$wrapper_class = 'porto-btn-ctn-left';
		break;
}

$wrapper_attrs = '';
if ( $animation_type ) {
	$wrapper_attrs .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
	if ( $animation_delay ) {
		$wrapper_attrs .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$wrapper_attrs .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
}

$output = '<div class="' . esc_attr( $wrapper_class ) . ' ' . esc_attr( $main_extra_class ) . '"' . $wrapper_attrs . '>' . $output . '</div>';

echo porto_filter_output( $output );
