<?php
    if (!defined('ABSPATH')) exit; // Exit if accessed directly
    ep_add_shortcode([
        'id'             => 'author_name',
        'callback'       => 'ep_shortcode_author_name',
        'name'           => __('Author Name', 'bdthemes-element-pack'),
        'type'           => 'single',
        'atts'           => [
            'class' => [
                'type'    => 'extra_css_class',
                'name'    => __('Extra CSS class', 'bdthemes-element-pack'),
                'desc'    => __('Additional CSS class name(s) separated by space(s)', 'bdthemes-element-pack'),
                'default' => '',
            ],
        ],
        'desc'           => __('Show Author Name', 'bdthemes-element-pack'),
    ]);

    function ep_shortcode_author_name($atts = null) {

        $atts = shortcode_atts(array('class' => ''), $atts, 'author-name');

        $output = '<span class="epsc-author-name' . Element_Pack_Shortcodes::ep_get_css_class($atts) . '">';
        $output .= get_the_author();
        $output .= '</span>';

        return $output;

    }
?>
