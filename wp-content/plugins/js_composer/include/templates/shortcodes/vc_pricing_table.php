<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Shortcode attributes
 * @var $atts
 * @var $content - shortcode content
 * Shortcode class
 * @var WPBakeryShortCode_Vc_Pricing_Table $this
 */

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
$this->buildTemplate( $atts, $content );
$cssClass = trim( 'vc_general ' . esc_attr( implode( ' ', $this->getTemplateVariable( 'css-class' ) ) ) );
$currency = isset( $atts['currency'] ) ? $atts['currency'] : '';
$price = isset( $atts['price'] ) ? $atts['price'] : '';
$period = isset( $atts['period'] ) ? $atts['period'] : '';
$element_id = uniqid( 'vc-pricing-table-' );

$output = '<section ' . ( ! empty( $atts['el_id'] ) ? ' id="' . esc_attr( $atts['el_id'] ) . '"' : '' ) . '>';
$output .= '<div id=' . esc_attr( $element_id ) . ' class="' . esc_attr( $cssClass ) . '"';
if ( $this->getTemplateVariable( 'inline-css' ) ) {
	$output .= ' style="' . esc_attr( implode( ' ', $this->getTemplateVariable( 'inline-css' ) ) ) . '"';
}
$output .= '>';
$output .= $this->getTemplateVariable( 'heading' );
$output .= $this->getTemplateVariable( 'subheading' );
$output .= '<div class="wpb-price-container">';
$output .= '<sup class="wpb-currency">' . esc_html( $currency ) . '</sup>';
$output .= '<strong class="wpb-price">' . esc_html( $price ) . '</strong>';
$output .= '<sub class="wpb-period">' .  esc_html( $period ) . '</sub>';
$output .= '</div>';

$output .= $this->getTemplateVariable( 'button' );
$output .= wpb_js_remove_wpautop( $content, true );
$output .= '</div>';
$output .= '</section>';

$output .= $this->getInlineStyle( $atts, $element_id );

return $output;
