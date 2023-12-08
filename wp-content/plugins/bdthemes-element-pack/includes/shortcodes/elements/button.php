<?php
    if (!defined('ABSPATH')) exit; // Exit if accessed directly
    ep_add_shortcode( [
        'id'       => 'button',
        'callback' => 'ep_shortcode_button',
        'name'     => __( 'Button', 'bdthemes-element-pack' ),
        'type'     => 'wrap',
        'atts'     => [
            'class' => [
                'type'    => 'extra_css_class',
                'name'    => __( 'Extra CSS class', 'bdthemes-element-pack' ),
                'desc'    => __( 'Additional CSS class name(s) separated by space(s)', 'bdthemes-element-pack' ),
                'default' => '',
            ],
            'link'  => [
                'type'    => 'url',
                'default' => '#'
            ],
            'type' => [
                'type'    => 'select',
                'default' => 'default',
            ],
            'style' => [
                'type'    => 'text',
                'default' => '',
            ],
        ],
        'content'  => [],
        'desc'     => __( 'Show Button', 'bdthemes-element-pack' ),
    ] );

    function ep_shortcode_button($atts = null, $content = null) {

        $atts = shortcode_atts( array('class' => '', 'link' => '#', 'type' => 'default', 'style' => ''), $atts, 'button' );

        $output = '<a class="epsc-button bdt-button ' . Element_Pack_Shortcodes::ep_get_css_class( $atts ) . ' bdt-button-' . $atts['type'] . '" href="' . $atts['link'] . '" style="' . $atts['style'] . '" >';
        $output .= do_shortcode( $content );
        $output .= '</a>';

        return $output;

    }

?>
