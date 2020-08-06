<?php

$output              = $fancytext_strings = $fancytext_prefix = $fancytext_suffix = $fancytext_align = $strings_font_family = $strings_font_style = $strings_font_size = $sufpref_color = $strings_line_height = $ticker_wait_time = $ticker_hover_pause = $el_class = '';
$prefsuf_font_family = $prefsuf_font_style = $prefix_suffix_font_size = $prefix_suffix_line_height = $sufpref_bg_color = '';
$id                  = uniqid( rand() );

extract(
	shortcode_atts(
		array(
			'fancytext_strings'         => '',
			'fancytext_prefix'          => '',
			'fancytext_suffix'          => '',
			'fancytext_tag'             => 'h2',
			'fancytext_align'           => 'center',
			'strings_font_family'       => '',
			'strings_use_theme_fonts'   => '',
			'strings_google_font'       => '',
			'strings_font_style'        => '',
			'strings_font_size'         => '',
			'sufpref_color'             => '',
			'strings_line_height'       => '',
			'ticker_wait_time'          => 2500,
			'ticker_hover_pause'        => '',
			'ticker_background'         => '',
			'fancytext_color'           => '',
			'prefsuf_font_family'       => '',
			'prefsuf_use_theme_fonts'   => '',
			'prefsuf_google_font'       => '',
			'prefsuf_font_style'        => '',
			'prefix_suffix_font_size'   => '',
			'prefix_suffix_line_height' => '',
			'sufpref_bg_color'          => '',
			'animation_effect'          => 'slide',
			'el_class'                  => '',
			'css_fancy_design'          => '',
			'animation_type'            => '',
			'animation_duration'        => 1000,
			'animation_delay'           => 0,
		),
		$atts
	)
);

wp_enqueue_script( 'porto_word_rotator' );

$string_inline_style = $word_rotate_inline = $prefsuf_style = $css_design_style = '';

if ( defined( 'VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG' ) ) {
	$css_design_style = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css_fancy_design, ' ' ), 'porto_fancytext', $atts );
}

if ( ( ! isset( $atts['strings_use_theme_fonts'] ) || 'yes' !== $atts['strings_use_theme_fonts'] ) && $strings_google_font ) {
	$google_fonts_data   = porto_sc_parse_google_font( $strings_google_font );
	$styles              = porto_sc_google_font_styles( $google_fonts_data );
	$word_rotate_inline .= esc_attr( $styles );
} elseif ( $strings_font_family ) {
	$word_rotate_inline .= 'font-family:\'' . esc_attr( $strings_font_family ) . '\';';
}
if ( $strings_font_style ) {
	$word_rotate_inline .= 'font-weight:' . esc_attr( $strings_font_style ) . ';';
}

if ( ( ! isset( $atts['prefsuf_use_theme_fonts'] ) || 'yes' !== $atts['prefsuf_use_theme_fonts'] ) && $prefsuf_google_font ) {
	$google_fonts_data1 = porto_sc_parse_google_font( $prefsuf_google_font );
	$styles             = porto_sc_google_font_styles( $google_fonts_data1 );
	$prefsuf_style     .= esc_attr( $styles );
} elseif ( $prefsuf_font_family ) {
	$prefsuf_style .= 'font-family:\'' . esc_attr( $prefsuf_font_family ) . '\';';
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

if ( $prefsuf_font_style ) {
	$prefsuf_style .= 'font-weight:' . esc_attr( $prefsuf_font_style ) . ';';
}

if ( $strings_font_size ) {
	$unit = trim( preg_replace( '/[0-9.]/', '', $strings_font_size ) );
	if ( ! $unit ) {
		$strings_font_size .= 'px';
	}
	$string_inline_style .= 'font-size:' . esc_attr( $strings_font_size ) . ';';
}
if ( $strings_line_height ) {
	$unit = trim( preg_replace( '/[0-9.]/', '', $strings_line_height ) );
	if ( ! $unit && (int) $strings_line_height > 3 ) {
		$strings_line_height .= 'px';
	}
	$string_inline_style .= 'line-height:' . esc_attr( $strings_line_height ) . ';';
}


if ( $prefix_suffix_font_size ) {
	$unit = trim( preg_replace( '/[0-9.]/', '', $prefix_suffix_font_size ) );
	if ( ! $unit ) {
		$prefix_suffix_font_size .= 'px';
	}
	$prefsuf_style .= 'font-size:' . esc_attr( $prefix_suffix_font_size ) . ' !important;';
}
if ( $prefix_suffix_line_height ) {
	$unit = trim( preg_replace( '/[0-9.]/', '', $prefix_suffix_line_height ) );
	if ( ! $unit && (int) $prefix_suffix_line_height > 3 ) {
		$prefix_suffix_line_height .= 'px';
	}
	$prefsuf_style .= 'line-height:' . esc_attr( $prefix_suffix_line_height ) . ' !important;';
}


if ( $sufpref_color ) {
	$prefsuf_style .= 'color:' . esc_attr( $sufpref_color ) . ';';
}
if ( $sufpref_bg_color ) {
	$prefsuf_style .= 'background :' . esc_attr( $sufpref_bg_color ) . ';';
}
if ( $fancytext_align ) {
	$string_inline_style .= 'text-align:' . esc_attr( $fancytext_align ) . ';';
}


$order   = array( "\r\n", "\n", "\r", '<br/>', '<br>' );
$replace = '|';

$str = str_replace( $order, $replace, $fancytext_strings );

$lines = explode( '|', $str );

$count_lines = count( $lines );


if ( $fancytext_color ) {
	$word_rotate_inline .= 'color:' . esc_attr( $fancytext_color ) . ';';
}
if ( $ticker_background ) {
	$word_rotate_inline .= 'background:' . esc_attr( $ticker_background ) . ';';
}
if ( 'bounce' == $animation_effect ) {
	$animation_effect = 'slide';
}
$classes = 'word-rotator ' . esc_attr( $animation_effect );
if ( $css_design_style ) {
	$classes .= ' ' . esc_attr( $css_design_style );
}
if ( $el_class ) {
	$classes .= ' ' . esc_attr( $el_class );
}

if ( 'true' != $ticker_hover_pause ) {
	$ticker_hover_pause = 'false';
}
$plugin_options = "{'waittime': " . esc_attr( $ticker_wait_time ) . ", 'pauseOnHover': " . esc_attr( $ticker_hover_pause ) . '}';

$attrs = '';
if ( $animation_type ) {
	$attrs .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
	if ( $animation_delay ) {
		$attrs .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$attrs .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
}

$output = '<' . esc_html( $fancytext_tag ) . ' class="' . esc_attr( $classes ) . '"' . $attrs . ' style="' . esc_attr( $string_inline_style ) . '" data-plugin-options="' . $plugin_options . '">';

if ( trim( $fancytext_prefix ) ) {
	$output .= '<span class="word-rotate-prefix" style="' . esc_attr( $prefsuf_style ) . '">' . esc_html( ltrim( $fancytext_prefix ) ) . '</span> ';
}

	$output .= '<span class="word-rotator-items' . ( $ticker_background ? ' has-bg' : '' ) . ( strpos( $animation_effect, 'type' ) !== false ? ' waiting' : '' ) . '"' . ' style="' . esc_attr( $word_rotate_inline ) . '">';
foreach ( $lines as $key => $line ) {
	$output .= '<b' . ( 0 === $key ? ' class="active"' : '' ) . '>' . strip_tags( $line ) . '</b>';
}
	$output .= '</span>';
if ( trim( $fancytext_suffix ) ) {
	$output .= ' <span class="word-rotate-suffix" style="' . esc_attr( $prefsuf_style ) . '">' . esc_html( rtrim( $fancytext_suffix ) ) . '</span>';
}
$output .= '</' . esc_html( $fancytext_tag ) . '>';

echo porto_filter_output( $output );
