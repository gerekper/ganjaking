<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $title
 * @var $el_class
 * @var $el_id
 * @var $type
 * @var $style
 * @var $legend
 * @var $animation
 * @var $tooltips
 * @var $stroke_color
 * @var $custom_stroke_color
 * @var $stroke_width
 * @var $values
 * @var $css
 * @var $css_animation
 * Shortcode class
 * @var WPBakeryShortCode_Vc_Round_Chart $this
 */
$el_class = $el_id = $title = $type = $style = $legend = $animation = $tooltips = $stroke_color = $stroke_width = $values = $css = $css_animation = $custom_stroke_color = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$base_colors = array(
	'normal' => array(
		'blue' => '#5472d2',
		'turquoise' => '#00c1cf',
		'pink' => '#fe6c61',
		'violet' => '#8d6dc4',
		'peacoc' => '#4cadc9',
		'chino' => '#cec2ab',
		'mulled-wine' => '#50485b',
		'vista-blue' => '#75d69c',
		'orange' => '#f7be68',
		'sky' => '#5aa1e3',
		'green' => '#6dab3c',
		'juicy-pink' => '#f4524d',
		'sandy-brown' => '#f79468',
		'purple' => '#b97ebb',
		'black' => '#2a2a2a',
		'grey' => '#ebebeb',
		'white' => '#ffffff',
		'default' => '#f7f7f7',
		'primary' => '#0088cc',
		'info' => '#58b9da',
		'success' => '#6ab165',
		'warning' => '#ff9900',
		'danger' => '#ff675b',
		'inverse' => '#555555',
	),
	'active' => array(
		'blue' => '#3c5ecc',
		'turquoise' => '#00a4b0',
		'pink' => '#fe5043',
		'violet' => '#7c57bb',
		'peacoc' => '#39a0bd',
		'chino' => '#c3b498',
		'mulled-wine' => '#413a4a',
		'vista-blue' => '#5dcf8b',
		'orange' => '#f5b14b',
		'sky' => '#4092df',
		'green' => '#5f9434',
		'juicy-pink' => '#f23630',
		'sandy-brown' => '#f57f4b',
		'purple' => '#ae6ab0',
		'black' => '#1b1b1b',
		'grey' => '#dcdcdc',
		'white' => '#f0f0f0',
		'default' => '#e8e8e8',
		'primary' => '#0074ad',
		'info' => '#3fafd4',
		'success' => '#59a453',
		'warning' => '#e08700',
		'danger' => '#ff4b3c',
		'inverse' => '#464646',
	),
);
$colors = array(
	'flat' => array(
		'normal' => $base_colors['normal'],
		'active' => $base_colors['active'],
	),
	'modern' => array(),
);
foreach ( $base_colors['normal'] as $name => $color ) {
	$colors['modern']['normal'][ $name ] = array(
		vc_colorCreator( $color, 7 ),
		$color,
	);
}
foreach ( $base_colors['active'] as $name => $color ) {
	$colors['modern']['active'][ $name ] = array(
		vc_colorCreator( $color, 7 ),
		$color,
	);
}

wp_enqueue_script( 'vc_round_chart' );

$class_to_filter = 'vc_chart vc_round-chart wpb_content_element';
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

$options = array();

if ( ! empty( $legend ) ) {
	$options[] = 'data-vc-legend="1"';
}

if ( ! empty( $tooltips ) ) {
	$options[] = 'data-vc-tooltips="1"';
}

if ( ! empty( $animation ) ) {
	$options[] = 'data-vc-animation="' . esc_attr( str_replace( 'easein', 'easeIn', $animation ) ) . '"';
}

if ( ! empty( $stroke_color ) ) {
	if ( 'custom' === $stroke_color ) {
		if ( $custom_stroke_color ) {
			$color = $custom_stroke_color;
		} else {
			$color = $base_colors['normal']['white'];
		}
	} else {
		$color = $base_colors['normal'][ $stroke_color ];
	}

	$options[] = 'data-vc-stroke-color="' . esc_attr( $color ) . '"';
}

if ( ! empty( $stroke_width ) ) {
	$options[] = 'data-vc-stroke-width="' . esc_attr( $stroke_width ) . '"';
}

$values = (array) vc_param_group_parse_atts( $values );
$data = array();

$labels = [];
$datasets = [];
$datasetValues = [];
$datasetColors = [];
foreach ( $values as $k => $v ) {

	if ( 'custom' === $style ) {
		if ( ! empty( $v['custom_color'] ) ) {
			$color = $v['custom_color'];
		} else {
			$color = $base_colors['normal']['grey'];
		}
	} else {
		$color = isset( $colors[ $style ]['normal'][ $v['color'] ] ) ? $colors[ $style ]['normal'][ $v['color'] ] : $v['normal']['color'];
	}
	$labels[] = isset( $v['title'] ) ? $v['title'] : '';
	$datasetValues[] = (int) ( isset( $v['value'] ) ? $v['value'] : 0 );
	$datasetColors[] = $color;
}

$options[] = 'data-vc-type="' . esc_attr( $type ) . '"';
$legendColor = isset( $atts['legend_color'] ) ? $atts['legend_color'] : 'black';
if ( 'custom' === $legendColor ) {
	$legendColor = isset( $atts['custom_legend_color'] ) ? $atts['custom_legend_color'] : 'black';
} else {
	$legendColor = vc_convert_vc_color( $legendColor );
}
$round_chart_data = [
	'labels' => $labels,
	'datasets' => [
		[
			'data' => $datasetValues,
			'backgroundColor' => $datasetColors,
		],
	],
];
$options[] = 'data-vc-values="' . esc_attr( wp_json_encode( $round_chart_data ) ) . '"';
$options[] = 'data-vc-legend-color="' . esc_attr( $legendColor ) . '"';
if ( '' !== $title ) {
	$title = '<h2 class="wpb_heading">' . $title . '</h4>';
}

$canvas_html = '<canvas class="vc_round-chart-canvas" width="1" height="1"></canvas>';
if ( ! empty( $el_id ) ) {
	$options[] = 'id="' . esc_attr( $el_id ) . '"';
}
$output = '
<div class="' . esc_attr( $css_class ) . '" ' . implode( ' ', $options ) . '>
	' . $title . '
	<div class="wpb_wrapper">
		' . $canvas_html . '
	</div>' . '
</div>' . '
';

return $output;
