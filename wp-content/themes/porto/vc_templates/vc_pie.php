<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $title
 * @var $el_class
 * @var $value
 * @var $units
 * @var $color
 * @var $custom_color
 * @var $label_value
 * @var $css
 *
 * Extra Params
 * @var $type : custom
 * @var $view
 * @var $view_size
 * @var $icon
 * @var $icon_color
 * @var $size
 * @var $trackcolor
 * @var $barcolor
 * @var $scalecolor
 * @var $speed
 * @var $line
 * @var $linecap
 *
 * Shortcode class
 * @var $this WPBakeryShortCode_Vc_Pie
 */
$title            = $output = '';
$is_wpb_rendering = defined( 'WPB_VC_VERSION' ) && $this instanceof WPBakeryShortCode_Vc_Pie;
$original_atts    = $atts;
if ( $is_wpb_rendering ) {
	$atts = $this->convertOldColorsToNew( $atts );
	$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
}
extract( $atts );


if ( 'default' == $type ) {

	if ( porto_is_ajax() ) {
		if ( wp_script_is( 'vc_pie', 'registered' ) && ! wp_script_is( 'vc_pie', 'done' ) ) {
			$wp_scripts = wp_scripts();
			$src        = $wp_scripts->registered['vc_pie']->src;
			echo "<script type='text/javascript' src='" . esc_url( $src ) . "'></script>";
		}
	} else {
		wp_enqueue_script( 'vc_pie' );
	}

	$colors = array(
		'blue'        => '#5472d2',
		'turquoise'   => '#00c1cf',
		'pink'        => '#fe6c61',
		'violet'      => '#8d6dc4',
		'peacoc'      => '#4cadc9',
		'chino'       => '#cec2ab',
		'mulled-wine' => '#50485b',
		'vista-blue'  => '#75d69c',
		'orange'      => '#f7be68',
		'sky'         => '#5aa1e3',
		'green'       => '#6dab3c',
		'juicy-pink'  => '#f4524d',
		'sandy-brown' => '#f79468',
		'purple'      => '#b97ebb',
		'black'       => '#2a2a2a',
		'grey'        => '#ebebeb',
		'white'       => '#ffffff',
	);

	if ( 'custom' === $color ) {
		$color = $custom_color;
	} else {
		$color = isset( $colors[ $color ] ) ? $colors[ $color ] : '';
	}

	if ( ! $color ) {
		$color = $colors['grey'];
	}

	$class_to_filter  = 'vc_pie_chart wpb_content_element';
	$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class );
	$css_class        = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

	$output  = '<div class= "' . esc_attr( $css_class ) . '" data-pie-value="' . esc_attr( $value ) . '" data-pie-label-value="' . esc_attr( $label_value ) . '" data-pie-units="' . esc_attr( $units ) . '" data-pie-color="' . esc_attr( $color ) . '">';
	$output .= '<div class="wpb_wrapper">';
	$output .= '<div class="vc_pie_wrapper">';
	$output .= '<span class="vc_pie_chart_back" style="border-color: ' . esc_attr( $color ) . '"></span>';
	$output .= '<span class="vc_pie_chart_value"></span>';
	$output .= '<canvas width="101" height="101"></canvas>';
	$output .= '</div>';

	if ( '' !== $title ) {
		$output .= '<h4 class="wpb_heading wpb_pie_chart_heading">' . porto_strip_script_tags( $title ) . '</h4>';
	}

	$output .= '</div>';
	$output .= '</div>';

	echo porto_filter_output( $output );
} else {
	if ( class_exists( 'WPBMap' ) ) {
		$sc = WPBMap::getShortCode( 'vc_pie' );
		if ( ! empty( $sc['params'] ) && class_exists( 'PortoShortcodesClass' ) && method_exists( 'PortoShortcodesClass', 'get_global_hashcode' ) ) {
			foreach ( $original_atts as $key => $item ) {
				if ( in_array( $key, array( 'title_porto_typography', 'value_porto_typography', 'title_pos', 'value_pos', 'icon_size' ) ) ) {
					$original_atts[ $key ] = str_replace( '"', '``', $item );
				}
			}
			$shortcode_class = ' wpb_custom_' . PortoShortcodesClass::get_global_hashcode( $original_atts, 'vc_pie', $sc['params'] );
			if ( empty( $el_class ) ) {
				$el_class = $shortcode_class;
			} else {
				$el_class .= ' ' . $shortcode_class;
			}
		}
		$internal_css = PortoShortcodesClass::generate_wpb_css( 'vc_pie', $original_atts );
	}

	wp_enqueue_script( 'easypiechart' );

	global $porto_settings;
	if ( empty( $barcolor ) ) {
		$barcolor = $porto_settings['skin-color'];
	}

	$options                        = array();
	$options['trackColor']          = $trackcolor;
	$options['barColor']            = $barcolor;
	$options['scaleColor']          = $scalecolor;
	$options['lineCap']             = $linecap;
	$options['lineWidth']           = $line;
	$options['size']                = $size;
	$options['animate']['duration'] = $speed;
	$options['labelValue']          = $label_value;
	$options                        = json_encode( $options );

	//data-label-value="' . esc_attr( $label_value ) . '"
	$css_class = 'circular-bar center';
	if ( $view ) {
		$css_class .= ' ' . $view;
	}
	if ( $view_size ) {
		$css_class .= ' circular-bar-' . $view_size;
	}
	if ( $is_wpb_rendering ) {
		$el_class = $this->getExtraClass( $el_class );
		if ( $el_class ) {
			$css_class .= ' ' . $el_class;
		}
		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $css_class . ' ' . vc_shortcode_custom_css_class( $css, ' ' ), $this->settings['base'], $atts );
	}
	$output = '<div class= "' . esc_attr( $css_class ) . '">';
	if ( ! empty( $internal_css ) ) {
		// only wpbakery frontend editor
		$output .= '<style>' . wp_strip_all_tags( $internal_css ) . '</style>';
	}
		$output .= '<div class="circular-bar-chart" data-percent="' . esc_attr( $value ) . '" data-plugin-options="' . esc_attr( $options ) . '" style="height:' . esc_attr( $size ) . 'px">';
	if ( 'only-icon' == $view && $icon ) {
		$output .= '<i class="' . esc_attr( $icon ) . '"' . ( $icon_color ? ' style="color:' . esc_attr( $icon_color ) . '"' : '' ) . '></i>';
	} elseif ( 'single-line' == $view ) {
		if ( $title ) {
			$output .= '<strong>' . porto_strip_script_tags( $title ) . '</strong>';
		}
	} else {
		if ( $title ) {
			$output .= '<strong>' . porto_strip_script_tags( $title ) . '</strong>';
		}
		$output .= '<label><span class="percent">0</span>' . esc_html( $units ) . '</label>';
	}
		$output .= '</div>';

	$output .= '</div>';
	echo porto_filter_output( $output );
}
