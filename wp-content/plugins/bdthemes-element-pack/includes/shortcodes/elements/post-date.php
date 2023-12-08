<?php
    if (!defined('ABSPATH')) exit; // Exit if accessed directly
    ep_add_shortcode([
        'id'             => 'post_date',
        'callback'       => 'ep_shortcode_post_date',
        'name'           => __('Post Date', 'bdthemes-element-pack'),
        'type'           => 'single',
        'atts'           => [
            'class' => [
                'type'    => 'extra_css_class',
                'name'    => __('Extra CSS class', 'bdthemes-element-pack'),
                'desc'    => __('Additional CSS class name(s) separated by space(s)', 'bdthemes-element-pack'),
                'default' => '',
            ],
        ],
        'desc'           => __('Show Post Date', 'bdthemes-element-pack'),
    ]);

    function ep_shortcode_post_date($atts = null) {

        $atts = shortcode_atts(array('class' => ''), $atts, 'post-date');

        $output = '<span class="epsc-post-date' . Element_Pack_Shortcodes::ep_get_css_class($atts) . '">';
        $output .= get_the_date();
        $output .= '</span>';

        return $output;

    }
?>
