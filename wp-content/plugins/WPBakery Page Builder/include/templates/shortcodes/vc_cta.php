<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Shortcode attributes
 * @var $atts
 * @var $content - shortcode content
 * Shortcode class
 * @var WPBakeryShortCode_Vc_Cta $this
 */

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
$this->buildTemplate( $atts, $content );
$containerClass = trim( 'vc_cta3-container ' . esc_attr( implode( ' ', $this->getTemplateVariable( 'container-class' ) ) ) );
$cssClass = trim( 'vc_general ' . esc_attr( implode( ' ', $this->getTemplateVariable( 'css-class' ) ) ) );
$output = '';

$output .= '<section class="' . esc_attr( $containerClass ) . '"' . ( ! empty( $atts['el_id'] ) ? ' id="' . esc_attr( $atts['el_id'] ) . '"' : '' ) . '>';
$output .= '<div class="' . esc_attr( $cssClass ) . '"';
if ( $this->getTemplateVariable( 'inline-css' ) ) {
	$output .= ' style="' . esc_attr( implode( ' ', $this->getTemplateVariable( 'inline-css' ) ) ) . '"';
}
$output .= '>'; // div
$output .= $this->getTemplateVariable( 'icons-top' );
$output .= $this->getTemplateVariable( 'icons-left' );

$output .= '<div class="vc_cta3_content-container">';
$output .= $this->getTemplateVariable( 'actions-top' );
$output .= $this->getTemplateVariable( 'actions-left' );
$output .= '<div class="vc_cta3-content">';
$output .= '<header class="vc_cta3-content-header">';
$output .= $this->getTemplateVariable( 'heading1' );
$output .= $this->getTemplateVariable( 'heading2' );
$output .= '</header>';
$output .= $this->getTemplateVariable( 'content' );
$output .= '</div>';
$output .= $this->getTemplateVariable( 'actions-bottom' );
$output .= $this->getTemplateVariable( 'actions-right' );
$output .= '</div>';
$output .= $this->getTemplateVariable( 'icons-bottom' );
$output .= $this->getTemplateVariable( 'icons-right' );
$output .= '</div></section>';

return $output;
