<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $tab_id
 * @var $title
 * @var $content - shortcode content
 *
 * Extra Params (will be use vc_tabs.php)
 * @var $show_icon
 * @var $icon_type
 * @var $icon_image
 * @var $icon
 * @var $icon_simleline
 * @var $label
 * @var $icon_skin
 * @var $icon_color
 * @var $icon_bg_color
 * @var $icon_border_color
 * @var $icon_wrap_border_color
 * @var $icon_shadow_color
 * @var $icon_hcolor
 * @var $icon_hbg_color
 * @var $icon_hborder_color
 * @var $icon_wrap_hborder_color
 * @var $icon_hshadow_color
 *
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Tab
 */
$output = '';
$atts   = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'tab-pane', $this->settings['base'] );
if ( vc_is_inline() ) {
	$css_class .= ' tab-content';
}
$output   .= '<div id="tab-' . ( empty( $tab_id ) ? sanitize_title( $title ) : esc_attr( $tab_id ) ) . '" class="' . esc_attr( $css_class ) . '">';
$output   .= ( '' === trim( $content ) ) ? esc_html__( 'Empty section. Edit page to add content here.', 'js_composer' ) : wpb_js_remove_wpautop( $content );
$output   .= '</div>';

echo porto_filter_output( $output );
