<?php
if (! defined('ABSPATH')) {
    die('-1');
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $el_class
 * @var $css
 * @var $el_id
 * @var $equal_height
 * @var $content_placement
 * @var $content - shortcode content
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Row_Inner
 */
$el_class = $equal_height = $content_placement = $css = $el_id = '';

//extra inner_row atts
$agro_row_prepad = $agro_lg_bgpos = $agro_lg_custom_bgpos = $agro_md_bgpos = $agro_md_custom_bgpos = $agro_sm_bgpos = $agro_sm_custom_bgpos = $agro_xs_bgpos = $agro_xs_custom_bgpos = $agro_md_hidebg = $agro_sm_hidebg = $agro_xs_hidebg = $agro_md_css = $agro_sm_css = $agro_xs_css = '';
//extra inner_row atts

$disable_element = '';
$output = $after_output = '';
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);

$el_class = $this->getExtraClass($el_class);
$css_classes = array(
    'row',
    $agro_row_prepad,
    $el_class,
    vc_shortcode_custom_css_class($css),
);

if ('yes' === $disable_element) {
    if (vc_is_page_editable()) {
        $css_classes[] = 'vc_hidden-lg vc_hidden-xs vc_hidden-sm vc_hidden-md';
    } else {
        return '';
    }
}

if (vc_shortcode_custom_css_has_property($css, array(
    'border',
    'background',
))) {
    $css_classes[] = 'nt-has-fill';
}

if (! empty($atts['gap'])) {
    $css_classes[] = 'vc_column-gap-' . $atts['gap'];
}

if (! empty($equal_height)) {
    $flex_row = true;
    $css_classes[] = 'vc_row-o-equal-height';
}

if (! empty($content_placement)) {
    $flex_row = true;
    $css_classes[] = 'vc_row-o-content-' . $content_placement;
}

if (! empty($flex_row)) {
    $css_classes[] = 'vc_row-flex';
}

$wrapper_attributes = array();
// build attributes for wrapper
if (! empty($el_id)) {
    $wrapper_attributes[] = 'id="' . esc_attr($el_id) . '"';
}

//craete uniq class for row data css
$agro_inrow_data_unique_class = 'nt_row_inner_1541'.mt_rand(15, 1000000000);
//add to custom css function
$agro_inrow_data = (class_exists('Vc_Manager')) ? agro_vc_extra_css($atts, $agro_inrow_data_unique_class, $agro_inrow_extra = '') : '';
$agro_inrow_class = $agro_inrow_data != '' ? $agro_inrow_data_unique_class : '';
$css_classes[] = $agro_inrow_data != '' ? $agro_inrow_class.' nt_row_inner-has-responsive-data' : '';

$css_class = preg_replace('/\s+/', ' ', apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, implode(' ', array_filter(array_unique($css_classes))), $this->settings['base'], $atts));
$wrapper_attributes[] = 'class="' . esc_attr(trim($css_class)) . '"';

$output .= '<div ' . implode(' ', $wrapper_attributes) . $agro_inrow_data .'>';
$output .= wpb_js_remove_wpautop($content);
$output .= '</div>';
$output .= $after_output;

echo agro_vc_sanitize_data($output);
