<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $title
 * @var $el_class
 * @var $collapsible
 * @var $disable_keyboard : removed
 * @var $active_tab
 * @var $content - shortcode content
 *
 * Extra Params
 * @var $use_accordion
 * @var $type
 * @var $size
 * @var $skin : 'custom',
 * @var $heading_color
 * @var $heading_bg_color
 *
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Accordion
 */
$output = '';
$atts   = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

//wp_enqueue_script('jquery-ui-accordion');

$id        = 'accordion' . rand();
$el_class  = $this->getExtraClass( $el_class );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'accordion ' . $el_class . ' ' . $type . ( 'custom' == $type && 'custom' != $skin ? ' accordion-' . $skin . ' ' : '' ) . ' ' . $size, $this->settings['base'] );

if ( 'custom' == $type && 'custom' == $skin && ( $heading_color || $heading_bg_color ) ) {
	$inline_style = '<style>';
	if ( $heading_color ) {
		$inline_style .= '#' . $id . '.accordion .card-header a { color: ' . esc_html( $heading_color ) . ' }';
	}
	if ( $heading_bg_color ) {
		$inline_style .= '#' . $id . '.accordion .card-header { background-color: ' . esc_html( $heading_bg_color ) . ' }';
	}
	$inline_style .= '</style>';
	$output       .= $inline_style;
}

$output .= '<div class="' . esc_attr( $css_class ) . '" id="' . $id . '" data-collapsible="' . esc_attr( $collapsible ) . '" data-active-tab="' . esc_attr( $active_tab ) . '"' . ( isset( $use_accordion ) && $use_accordion ? ' data-use-accordion="yes"' : '' ) . '>'; //data-interval="'.$interval.'"
$output .= wpb_widget_title(
	array(
		'title'      => $title,
		'extraclass' => 'wpb_accordion_heading',
	)
);

$output .= wpb_js_remove_wpautop( $content );
$output .= '</div>';

echo porto_filter_output( $output );
