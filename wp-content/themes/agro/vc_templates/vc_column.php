<?php
if (! defined('ABSPATH')) {
    die('-1');
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $el_class
 * @var $width
 * @var $css
 * @var $offset
 * @var $content - shortcode content
 * @var $css_animation
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Column
 */
$el_class = $width = $css = $offset = $css_animation = '';

$agro_lg_bgpos = $agro_md_bgpos = $agro_sm_bgpos  = $agro_xs_bgpos = '';

$agro_lg_custom_bgpos = $agro_md_custom_bgpos = $agro_sm_custom_bgpos = $agro_xs_custom_bgpos = '';

$agro_md_hidebg = $agro_sm_hidebg = $agro_xs_hidebg = '';

$agro_md_css = $agro_sm_css = $agro_xs_css = '';

$agro_disable_column = '';
$agro_xl_column_width = $agro_xl_column_offset = '';

//extra column atts

$output = '';
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);

$width = wpb_translateColumnWidthToSpan($width);
$width = vc_column_offset_class_merge($offset, $width);

//extra code
$width = $agro_disable_column == 'yes' ? '' : $width;

$css_classes = array(
    $this->getExtraClass($el_class) . $this->getCSSAnimation($css_animation),
    'nt-column',
    $width,
    $agro_xl_column_width,
    $agro_xl_column_offset,
);


if (vc_shortcode_custom_css_has_property($css, array(
    'border',
    'background',
))) {
    $css_classes[] = 'nt-col-has-fill';
}

//craete uniq class for row data css
$agro_column_data_unique_class = 'nt_column_1541'.mt_rand(15, 1000000000);
//add to custom css function
$agro_column_data = (class_exists('Vc_Manager')) ? agro_vc_extra_css($atts, $agro_column_data_unique_class, $agro_column_extra = '') : '';
$agro_column_class = $agro_column_data != '' ? ' '.$agro_column_data_unique_class : '';
$css_classes[] = $agro_column_data != '' ? 'nt-col-has-responsive-data' : '';

$wrapper_attributes = array();
if (! empty($el_id)) {
    $wrapper_attributes[] = 'id="' . esc_attr($el_id) . '"';
}

$css_class = preg_replace('/\s+/', ' ', apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, implode(' ', array_filter($css_classes)), $this->settings['base'], $atts));
$wrapper_attributes[] = 'class="' . esc_attr(trim($css_class)) . '"';


$output .= '<div ' . implode(' ', $wrapper_attributes) .'>'; // column
$output .= '<div class="nt-shortcode-wrapper ' . esc_attr(trim(vc_shortcode_custom_css_class($css))) .$agro_column_class.'"'.$agro_column_data.'>'; // shortcode wrapper
$output .= wpb_js_remove_wpautop($content); // shortcode content
$output .= '</div>'; // end shortcode wrapper
$output .= '</div>'; // end column


echo agro_vc_sanitize_data($output);
