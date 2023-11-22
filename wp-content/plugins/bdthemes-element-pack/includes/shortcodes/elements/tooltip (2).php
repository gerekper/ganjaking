<?php
    if (!defined('ABSPATH')) exit; // Exit if accessed directly
    ep_add_shortcode([
        'id'             => 'tooltip',
        'callback'       => 'ep_shortcode_tooltip',
        'name'           => __('Tooltip', 'bdthemes-element-pack'),
        'type'           => 'wrap',
        'atts'           => [
            'class' => [
                'type'    => 'extra_css_class',
                'name'    => __('Extra CSS class', 'bdthemes-element-pack'),
                'desc'    => __('Additional CSS class name(s) separated by space(s)', 'bdthemes-element-pack'),
                'default' => '',
            ],
            'position' => [
                'type' => 'select',
                'default' => 'top',
            ],
            'tooltip_text' => [
                'type' => 'text',
                'default' => 'Tooltip',
            ]

        ],
        'content'  => [],
        'desc' => __('Show Tooltip', 'bdthemes-element-pack'),
    ]);

    function ep_shortcode_tooltip($atts = null, $content = null ) {

        $atts = shortcode_atts(array('class' => '', 'position' =>'top-center', 'tooltip_text' => 'This is Tooltip'), $atts, 'tooltip');

        $output = '<span class="epsc-tooltip' . Element_Pack_Shortcodes::ep_get_css_class($atts) . '" bdt-tooltip="title: '.$atts['tooltip_text'].'; pos: '.$atts['position'].'">';
        $output .= do_shortcode($content);
        $output .= '</span>';

        return $output;

    }
?>
