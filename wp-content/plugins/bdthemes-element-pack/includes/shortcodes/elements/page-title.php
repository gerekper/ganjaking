<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
    ep_add_shortcode([
        'id'             => 'page_title',
        'callback'       => 'ep_shortcode_page_title',
        'name'           => __('Page Title', 'bdthemes-element-pack'),
        'type'           => 'single',
        'atts'           => [
            'class' => [
                'type'    => 'extra_css_class',
                'name'    => __('Extra CSS class', 'bdthemes-element-pack'),
                'desc'    => __('Additional CSS class name(s) separated by space(s)', 'bdthemes-element-pack'),
                'default' => '',
            ],
        ],
        'desc'           => __('Show Page Title', 'bdthemes-element-pack'),
    ]);

    function ep_shortcode_page_title($atts = null) {

        $atts = shortcode_atts(array('class' => ''), $atts, 'page-title');

        $output = '<span class="epsc-page-title' . Element_Pack_Shortcodes::ep_get_css_class($atts) . '">';
        $output .= get_the_title();
        $output .= '</span>';

        return $output;

    }
?>
