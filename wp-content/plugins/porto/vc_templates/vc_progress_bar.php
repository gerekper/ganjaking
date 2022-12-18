<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $title
 * @var $values
 * @var $units
 * @var $bgcolor
 * @var $custombgcolor
 * @var $customtxtcolor
 * @var $options
 * @var $el_class
 * @var $css
 *
 * Extra Params
 * @var $contextual
 * @var $tooltip
 * @var $animation
 * @var $border_radius
 * @var $size
 * @var $min_width
 *
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Progress_Bar
 */

$original_atts = $atts;

$output = '';
$atts   = vc_map_get_attributes( $this->getShortcode(), $atts );
$atts   = $this->convertAttributesToNewProgressBar( $atts );

extract( $atts );

if ( porto_is_ajax() ) {
	if ( wp_script_is( 'vc_waypoints', 'registered' ) && ! wp_script_is( 'vc_waypoints', 'done' ) ) {
		$wp_scripts = wp_scripts();
		$src        = $wp_scripts->registered['vc_waypoints']->src;
		echo "<script type='text/javascript' src='" . esc_url( $src ) . "'></script>";
	}
} else {
	wp_enqueue_script( 'vc_waypoints' );
}

$el_class = $this->getExtraClass( $el_class );

$bar_options = array();
$options     = explode( ',', $options );
if ( in_array( 'animated', $options, true ) ) {
	$bar_options[] = 'animated';
}
if ( in_array( 'striped', $options, true ) ) {
	$bar_options[] = 'striped';
}

if ( $contextual ) {
	$bar_options [] = ' progress-bar-' . $contextual;
}

if ( 'custom' === $bgcolor && '' !== $custombgcolor ) {
	$custombgcolor = vc_get_css_color( 'background-color', $custombgcolor );
	if ( '' !== $customtxtcolor ) {
		$customtxtcolor = ' style="' . vc_get_css_color( 'color', $customtxtcolor ) . '"';
	}
	$bgcolor = '';
} else {
	$custombgcolor  = '';
	$customtxtcolor = '';
	$bgcolor        = 'vc_progress-bar-color-' . esc_attr( $bgcolor );
	$el_class      .= ' ' . $bgcolor;
}
if ( '' !== $bgcolor ) {
	$bgcolor = ' ' . $bgcolor;
}

$class_to_filter  = 'vc_progress_bar wpb_content_element';
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . ' ' . $this->getCSSAnimation( $css_animation );
$css_class        = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, trim( $class_to_filter ), $this->settings['base'], $atts );

if ( class_exists( 'WPBMap' ) ) {
	$sc = WPBMap::getShortCode( 'vc_progress_bar' );
	if ( ! empty( $sc['params'] ) && class_exists( 'PortoShortcodesClass' ) && method_exists( 'PortoShortcodesClass', 'get_global_hashcode' ) ) {
		foreach ( $original_atts as $key => $item ) {
			if ( false !== strpos( $item, '"' ) ) {
				$original_atts[ $key ] = str_replace( '"', '``', $item );
			}
		}
		$css_class   .= ' wpb_custom_' . PortoShortcodesClass::get_global_hashcode( $original_atts, 'vc_progress_bar', $sc['params'] );
		$internal_css = PortoShortcodesClass::generate_wpb_css( 'vc_progress_bar', $original_atts );
	}
}

$output = '<div class="' . esc_attr( $css_class ) . '">';
if ( ! empty( $internal_css ) ) {
	// only wpbakery frontend editor
	$output .= wp_strip_all_tags( $internal_css );
}
$output .= wpb_widget_title(
	array(
		'title'      => $title,
		'extraclass' => 'wpb_progress_bar_heading',
	)
);

$values           = (array) vc_param_group_parse_atts( $values );
$max_value        = 0.0;
$graph_lines_data = array();
foreach ( $values as $data ) {
	$new_line             = $data;
	$new_line['value']    = isset( $data['value'] ) ? $data['value'] : 0;
	$new_line['label']    = isset( $data['label'] ) ? $data['label'] : '';
	$new_line['bgcolor']  = isset( $data['color'] ) && 'custom' !== $data['color'] ? '' : $custombgcolor;
	$new_line['txtcolor'] = isset( $data['color'] ) && 'custom' !== $data['color'] ? '' : $customtxtcolor;
	if ( isset( $data['customcolor'] ) && ( ! isset( $data['color'] ) || 'custom' === $data['color'] ) ) {
		$new_line['bgcolor'] = 'background-color: ' . esc_attr( $data['customcolor'] ) . ';';
	}
	if ( isset( $data['customtxtcolor'] ) && ( ! isset( $data['color'] ) || 'custom' === $data['color'] ) ) {
		$new_line['txtcolor'] = ' style="color: ' . esc_attr( $data['customtxtcolor'] ) . ';"';
	}

	if ( $max_value < (float) $new_line['value'] ) {
		$max_value = $new_line['value'];
	}
	$graph_lines_data[] = $new_line;
}

foreach ( $graph_lines_data as $line ) {
	$output .= '<div class="progress-label"><span' . $line['txtcolor'] . '>' . wp_kses_post( $line['label'] ) . '</span></div>';
	$unit    = ( '' !== $units ) ? ' <span class="vc_label_units">' . wp_kses_post( $line['value'] . $units ) . '</span>' : '';
	$output .= '<div class="vc_general vc_single_bar progress' . ( isset( $line['color'] ) && 'custom' !== $line['color'] ? '' : esc_attr( $bgcolor ) ) . ( $border_radius && 'custom' != $border_radius ? ' progress-' . esc_attr( $border_radius ) : '' ) . ( $size && 'custom' != $size ? ' progress-' . esc_attr( $size ) : '' ) . ( ( isset( $line['color'] ) && 'custom' !== $line['color'] ) ?
			' vc_progress-bar-color-' . esc_attr( $line['color'] ) : '' )
		. '">';
	if ( $max_value > 100.00 ) {
		$percentage_value = (float) $line['value'] > 0 && $max_value > 100.00 ? round( (float) $line['value'] / $max_value * 100, 4 ) : 0;
	} else {
		$percentage_value = $line['value'];
	}
	$style = '';
	if ( $line['bgcolor'] || $min_width || ! $animation ) {
		$style = ' style="';
		if ( $line['bgcolor'] ) {
			$style .= $line['bgcolor'];
		}
		if ( $min_width ) {
			$style .= 'min-width:' . esc_attr( $min_width ) . ';';
		} elseif ( ! $animation ) {
			$style .= 'min-width:' . esc_attr( $percentage_value ) . '%';
		}
		$style .= '"';
	}
	$output .= '<span class="vc_bar progress-bar ' . esc_attr( implode( ' ', $bar_options ) ) . '" data-percentage-value="' . esc_attr( $percentage_value ) . '" data-value="' . esc_attr( $line['value'] ) . '"' . $style . '>';
	if ( $unit ) {
		if ( $tooltip ) {
			$output .= '<span class="progress-bar-tooltip">' . $unit . '</span>';
		} else {
			$output .= $unit;
		}
	}
	$output .= '</span></div>';
}

$output .= '</div>';

echo porto_filter_output( $output );
