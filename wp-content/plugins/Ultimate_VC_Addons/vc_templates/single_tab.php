<?php
/**
 * Single Tab file.
 *
 * @package Single Tab.
 */

$output                = '';
$ult_title             = '';
$tab_id                = '';
$font_icons_position   = '';
$icon_type             = '';
$icon                  = '';
$icon_color            = '';
$icon_hover_color      = '';
$icon_size             = '';
$icon_background_color = '';
$icon_margin_bottom    = '';
$icon_margin_left      = '';

extract( shortcode_atts( $this->predefined_atts, $atts ) ); //phpcs:ignore WordPress.PHP.DontExtract.extract_extract

global $tabarr;

$tabarr[] = array(
	'title'               => $ult_title,
	'tab_id'              => $tab_id,
	'font_icons_position' => $font_icons_position,
	'icon_type'           => $icon_type,
	'icon'                => $icon,
	'icon_color'          => $icon_color,
	'icon_hover_color'    => $icon_hover_color,
	'icon_size'           => $icon_size,
	'content'             => $content,
	'icon_margin'         => $icon_margin,
);


if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) {

	$admn = __( 'Empty tab. Edit page to add content here.', 'js_composer' );
} else {
	$admn = '';
}
$tabcont = wpb_js_remove_wpautop( $content );

$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'wpb_tab ui-tabs-panel wpb_ui-tabs-hide vc_clearfix ult_back', $this->settings['base'], $atts );
$output   .= "\n\t\t\t" . '<div  class="ult_tabitemname"  >';
$output   .= ( '' == $content || ' ' == $content ) ? esc_html( $admn ) : "\n\t\t\t\t" . wpb_js_remove_wpautop( $content );
$output   .= "\n\t\t\t" . '</div> ';
return $output;
