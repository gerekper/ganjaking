<?php

$icon_type  = $icon_img = $img_width = $icon = $icon_color = $icon_color_bg = $icon_size = $icon_style = $icon_border_style = $icon_border_radius = $icon_color_border = $icon_border_size = $icon_border_spacing = $icon_link = $el_class = $icon_animation = $animation_type = $counter_title = $counter_value = $icon_position = $font_size_title = $font_size_counter = $counter_font = $title_font = $speed = $counter_sep = $counter_suffix = $counter_prefix = $counter_decimal = $counter_color_txt = $desc_font_line_height = $title_font_line_height = '';
$title_font = $title_font_style = $title_font_size = $desc_font = $desc_font_style = $desc_font_size = $desc_font_color = $suf_pref_typography = $suf_pref_font = $suf_pref_font_style = $suf_pref_font_color = $suf_pref_font_size = $suf_pref_line_height = '';
extract(
	shortcode_atts(
		array(
			'icon_type'                => 'fontawesome',
			'icon'                     => '',
			'icon_simpleline'          => '',
			'icon_porto'               => '',
			'icon_img'                 => '',
			'img_width'                => '48',
			'icon_size'                => '32',
			'icon_color'               => '#333333',
			'icon_style'               => 'none',
			'icon_color_bg'            => '#ffffff',
			'icon_color_border'        => '#333333',
			'icon_border_style'        => '',
			'icon_border_size'         => '1',
			'icon_border_radius'       => '500',
			'icon_border_spacing'      => '50',
			'icon_link'                => '',
			'icon_animation'           => '',
			'animation_type'           => '',
			'counter_title'            => '',
			'counter_value'            => '1250',
			'counter_sep'              => ',',
			'counter_suffix'           => '',
			'counter_prefix'           => '',
			'counter_decimal'          => '.',
			'icon_position'            => 'top',
			'speed'                    => '3',
			'font_size_title'          => '',
			'font_size_counter'        => '',
			'counter_color_txt'        => '',
			'title_font'               => '',
			'title_use_theme_fonts'    => '',
			'title_google_font'        => '',
			'title_font_style'         => '',
			'title_font_size'          => '',
			'title_font_line_height'   => '',
			'desc_font'                => '',
			'desc_use_theme_fonts'     => '',
			'desc_google_font'         => '',
			'desc_font_style'          => '',
			'desc_font_size'           => '',
			'desc_font_color'          => '',
			'desc_font_line_height'    => '',
			'el_class'                 => '',
			'suf_pref_font'            => '',
			'suf_pref_use_theme_fonts' => '',
			'suf_pref_google_font'     => '',
			'suf_pref_font_color'      => '',
			'suf_pref_font_size'       => '',
			'suf_pref_line_height'     => '',
			'suf_pref_font_style'      => '',
			'css_stat_counter'         => '',
			'className'                => '',
		),
		$atts
	)
);

wp_enqueue_script( 'countup' );
wp_enqueue_script( 'porto_shortcodes_countup_loader_js' );

switch ( $icon_type ) {
	case 'simpleline':
		$icon = $icon_simpleline;
		break;
	case 'porto':
		$icon = $icon_porto;
		break;
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


if ( defined( 'VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG' ) && $css_stat_counter ) {
	$css_stat_counter = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css_stat_counter, ' ' ), 'stat_counter', $atts );
}

$class = $title_style = $desc_style = $suf_pref_style = '';
if ( $icon || $icon_img ) {
	$stats_icon = do_shortcode( '[porto_icon icon_type="' . $icon_type . '" icon="' . $icon . '" icon_img="' . $icon_img . '" img_width="' . $img_width . '" icon_size="' . $icon_size . '" icon_color="' . $icon_color . '" icon_style="' . $icon_style . '" icon_color_bg="' . $icon_color_bg . '" icon_color_border="' . $icon_color_border . '"  icon_border_style="' . $icon_border_style . '" icon_border_size="' . $icon_border_size . '" icon_border_radius="' . $icon_border_radius . '" icon_border_spacing="' . $icon_border_spacing . '" icon_link="' . $icon_link . '" icon_animation="' . $icon_animation . '"]' );
} else {
	$stats_icon = '';
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
	if ( $title_font_size || ( isset( $atts['title_google_font_style_font_size'] ) && ! empty( isset( $atts['title_google_font_style_font_size']['size'] ) ) ) ) {
		$font_size_title = '';
	}

	if ( $title_font_size ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $title_font_size ) );
		if ( ! $unit ) {
			$title_font_size .= 'px';
		}
		$title_style .= 'font-size:' . esc_attr( $title_font_size ) . ';';
	}
	if ( $title_font_line_height ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $title_font_line_height ) );
		if ( ! $unit && (int) $title_font_line_height > 3 ) {
			$title_font_line_height .= 'px';
		}
		$title_style .= 'line-height:' . esc_attr( $title_font_line_height ) . ';';
	}
}

if ( empty( $atts['desc_font_porto_typography'] ) ) {
	if ( ( ! isset( $atts['desc_use_theme_fonts'] ) || 'yes' !== $atts['desc_use_theme_fonts'] ) && $desc_google_font ) {
		$google_fonts_data1 = porto_sc_parse_google_font( $desc_google_font );
		$styles             = porto_sc_google_font_styles( $google_fonts_data1 );
		$desc_style        .= esc_attr( $styles );
	} elseif ( $desc_font ) {
		$desc_style .= 'font-family:\'' . esc_attr( $desc_font ) . '\';';
	}
	if ( $desc_font_style ) {
		$desc_style .= 'font-weight:' . esc_attr( $desc_font_style ) . ';';
	}

	if ( $desc_font_size || $suf_pref_font_size || ( isset( $atts['desc_google_font_style_font_size'] ) && ! empty( isset( $atts['desc_google_font_style_font_size']['size'] ) ) ) || ( isset( $atts['suf_pref_google_font_style_font_size'] ) && ! empty( isset( $atts['suf_pref_google_font_style_font_size']['size'] ) ) ) ) {
		$font_size_counter = '';
	}

	if ( $desc_font_size ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $desc_font_size ) );
		if ( ! $unit ) {
			$desc_font_size .= 'px';
		}
		$desc_style .= 'font-size:' . esc_attr( $desc_font_size ) . ';';
	}
	if ( $desc_font_line_height ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $desc_font_line_height ) );
		if ( ! $unit && (int) $desc_font_line_height > 3 ) {
			$desc_font_line_height .= 'px';
		}
		$desc_style .= 'line-height:' . esc_attr( $desc_font_line_height ) . ';';
	}
}
if ( $desc_font_color || $counter_color_txt ) {
	$desc_style .= 'color:' . esc_attr( $desc_font_color ? $desc_font_color : $counter_color_txt ) . ';';
}

if ( $counter_color_txt ) {
	$title_style .= 'color:' . esc_attr( $counter_color_txt ) . ';';
}

if ( 'none' !== $animation_type ) {
	$css_trans = 'data-appear-animation="' . esc_attr( $animation_type ) . '"';
}

if ( $font_size_counter ) {
	$counter_font = 'font-size:' . esc_attr( $font_size_counter ) . 'px;';
}

if ( $font_size_title ) {
	$title_style .= 'font-size:' . esc_attr( $font_size_title ) . 'px;';
}

if ( empty( $atts['suf_pref_font_porto_typography'] ) ) {
	if ( ( ! isset( $atts['suf_pref_use_theme_fonts'] ) || 'yes' !== $atts['suf_pref_use_theme_fonts'] ) && $suf_pref_google_font ) {
		$google_fonts_data2 = porto_sc_parse_google_font( $suf_pref_google_font );
		$styles             = porto_sc_google_font_styles( $google_fonts_data2 );
		$suf_pref_style    .= esc_attr( $styles );
	} elseif ( $suf_pref_font ) {
		$suf_pref_style .= 'font-family:\'' . esc_attr( $suf_pref_font ) . '\';';
	}
	if ( $suf_pref_font_style ) {
		$suf_pref_style .= 'font-weight:' . esc_attr( $suf_pref_font_style ) . ';';
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
if ( isset( $google_fonts_data2 ) && $google_fonts_data2 ) {
	$google_fonts_arr[] = $google_fonts_data2;
}
if ( ! empty( $google_fonts_arr ) ) {
	porto_sc_enqueue_google_fonts( $google_fonts_arr );
}
if ( empty( $atts['suf_pref_font_porto_typography'] ) ) {
	if ( $suf_pref_font_size ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $suf_pref_font_size ) );
		if ( ! $unit ) {
			$suf_pref_font_size .= 'px';
		}
		$suf_pref_style .= 'font-size:' . esc_attr( $suf_pref_font_size ) . ';';
	}
	if ( $suf_pref_line_height ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $suf_pref_line_height ) );
		if ( ! $unit && (int) $suf_pref_line_height > 3 ) {
			$suf_pref_line_height .= 'px';
		}
		$suf_pref_style .= 'line-height:' . esc_attr( $suf_pref_line_height ) . ';';
	}
}
if ( $suf_pref_font_color ) {
	$suf_pref_style .= 'color:' . esc_attr( $suf_pref_font_color );
}
if ( function_exists( 'porto_is_elementor_preview' ) && porto_is_elementor_preview() ) {
	$suf_pref_style .= 'display:inline-block;';
}


if ( $el_class ) {
	$class .= ' ' . $el_class;
}
$ic_position = 'stats-' . $icon_position;
$ic_class    = 'porto-sicon-' . $icon_position;
$output      = '<div class="stats-block ' . esc_attr( $ic_position ) . ' ' . esc_attr( $class ) . ' ' . esc_attr( $css_stat_counter ) . '">';
	$id      = 'counter_' . uniqid( rand() );
if ( '' == $counter_sep ) {
	$counter_sep = 'none';
}
if ( '' == $counter_decimal ) {
	$counter_decimal = 'none';
}
if ( 'right' !== $icon_position && $stats_icon ) {
	$output .= '<div class="' . esc_attr( $ic_class ) . '">' . $stats_icon . '</div>';
}
	$output .= '<div class="stats-desc">';
if ( '' !== $counter_prefix ) {
	$output .= '<div class="counter_prefix mycust" style="' . esc_attr( $counter_font ) . ' ' . esc_attr( $suf_pref_style ) . '">' . wp_kses_post( $counter_prefix ) . '</div>';
}
	$counter_init_value = isset( $counter_init_value ) ? $counter_init_value : 0;
		$output .= '<div id="' . esc_attr( $id ) . '" data-id="' . esc_attr( $id ) . '" class="stats-number" style="' . esc_attr( $counter_font ) . ' ' . esc_attr( $desc_style ) . '" data-speed="' . esc_attr( $speed ) . '" data-counter-value="' . esc_attr( $counter_value ) . '" data-separator="' . esc_attr( $counter_sep ) . '" data-decimal="' . esc_attr( $counter_decimal ) . '">' . intval( $counter_init_value ) . '</div>';
if ( '' !== $counter_suffix ) {
	$output .= '<div class="counter_suffix mycust" style="' . esc_attr( $counter_font ) . ' ' . esc_attr( $suf_pref_style ) . '">' . wp_kses_post( $counter_suffix ) . '</div>';
}

		$title_attrs_escaped = ' style="' . esc_attr( $title_style ) . '"';
if ( isset( $title_attrs ) ) {
	$title_attrs_escaped = $title_attrs . $title_attrs_escaped;
} else {
	$title_attrs_escaped = 'class="stats-text"' . $title_attrs_escaped;
}
		$output .= '<div ' . $title_attrs_escaped . '>' . porto_strip_script_tags( $counter_title ) . '</div>';
	$output     .= '</div>';
if ( 'right' == $icon_position && $stats_icon ) {
	$output .= '<div class="' . esc_attr( $ic_class ) . '">' . $stats_icon . '</div>';
}
$output .= '</div>';

echo porto_filter_output( $output );

global $porto_shortcode_counter_use;
if ( wp_script_is( 'countup', 'registered' ) && ( ! isset( $porto_shortcode_counter_use ) || ! $porto_shortcode_counter_use ) ) :
	$porto_shortcode_counter_use = true;
	?>
<script>
	jQuery(document).ready(function($) {
		if (typeof countUp == "undefined") {
			var c = document.createElement("script");
			c.src = "<?php echo wp_scripts()->registered['countup']->src; ?>";
			if (!$('script[src="' + c.src + '"]').length) {
				document.getElementsByTagName("body")[0].appendChild(c);
			}
			c = document.createElement("script");
			c.src = "<?php echo wp_scripts()->registered['porto_shortcodes_countup_loader_js']->src; ?>";
			if (!$('script[src="' + c.src + '"]').length) {
				document.getElementsByTagName("body")[0].appendChild(c);
			}
		}
	});
</script>
<?php endif; ?>
