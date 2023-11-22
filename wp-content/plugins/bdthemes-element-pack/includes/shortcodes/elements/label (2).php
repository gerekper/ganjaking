<?php
    if (!defined('ABSPATH')) exit; // Exit if accessed directly
    ep_add_shortcode([
        'id'             => 'label',
        'callback'       => 'ep_shortcode_label',
        'name'           => __('Label', 'bdthemes-element-pack'),
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
        'desc'           => __('Show Label', 'bdthemes-element-pack'),
    ]);

    function ep_shortcode_label($atts = null, $content = null) {

        $atts = shortcode_atts(array('class' => '', 'type' => 'default', 'style' => ''), $atts, 'label');

        $output = '<span class="epsc-label bdt-label' . Element_Pack_Shortcodes::ep_get_css_class($atts) . ' bdt-label-' .$atts['type']. '" style="'.$atts['style'].'">';
        $output .= do_shortcode( $content );
        $output .= '</span>';

        return $output;

    }
?>
