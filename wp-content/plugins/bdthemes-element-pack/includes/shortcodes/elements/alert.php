<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

ep_add_shortcode([
    'id'             => 'alert',
    'callback'       => 'ep_shortcode_alert',
    'name'           => __('Alert', 'bdthemes-element-pack'),
    'type'           => 'wrap',
    'atts'           => [
        'class' => [
            'type'    => 'extra_css_class',
            'name'    => __('Extra CSS class', 'bdthemes-element-pack'),
            'desc'    => __('Additional CSS class name(s) separated by space(s)', 'bdthemes-element-pack'),
            'default' => '',
        ],
        'type' => [
            'type' => 'select',
            'default' => 'default',
        ],
        'style' => [
            'type' => 'text',
            'default' => '',
        ]

    ],
    'content'  => [],
    'desc'     => __('Show Alert', 'bdthemes-element-pack'),
]);

function ep_shortcode_alert($atts = null, $content = null) {
    $atts = shortcode_atts(array('class' => '', 'type' => 'default', 'style' => ''), $atts, 'alert');
    $output = '<div class="epsc-alert ' . Element_Pack_Shortcodes::ep_get_css_class($atts) . ' bdt-alert-' . $atts['type'] . '" style="' . $atts['style'] . '" bdt-alert >';
    $output .= '<a class="bdt-alert-close" bdt-close></a>';
    $output .= do_shortcode($content);
    $output .= '</div>';

    return $output;
}
