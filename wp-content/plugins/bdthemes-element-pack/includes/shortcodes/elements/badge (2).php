<?php
    if (!defined('ABSPATH')) exit; // Exit if accessed directly
    ep_add_shortcode([
        'id'             => 'badge',
        'callback'       => 'ep_shortcode_badge',
        'name'           => __('Badge', 'bdthemes-element-pack'),
        'type'           => 'wrap',
        'atts'           => [
            'class' => [
                'type'    => 'extra_css_class',
                'name'    => __('Extra CSS class', 'bdthemes-element-pack'),
                'desc'    => __('Additional CSS class name(s) separated by space(s)', 'bdthemes-element-pack'),
                'default' => '',
            ]
        ],
        'content'  => [],
        'desc'     => __('Show Badge', 'bdthemes-element-pack'),
    ]);

    function ep_shortcode_badge($atts = null, $content = null) {

        $atts = shortcode_atts(array('class' => ''), $atts, 'badge');

        $output = '<span class="epsc-badge bdt-badge' . Element_Pack_Shortcodes::ep_get_css_class($atts) . '">';
        $output .= do_shortcode( $content );
        $output .= '</span>';

        return $output;

    }
?>
