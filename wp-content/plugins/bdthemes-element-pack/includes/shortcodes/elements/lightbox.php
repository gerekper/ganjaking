<?php
    if (!defined('ABSPATH')) exit; // Exit if accessed directly
    ep_add_shortcode( [
        'id'       => 'lightbox',
        'callback' => 'ep_shortcode_lightbox',
        'name'     => __( 'Lightbox', 'bdthemes-element-pack' ),
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
                'default' => 'https://youtu.be/aqz-KE-bpKQ'
            ],
            'caption' => [
                'type'    => 'text',
                'default' => 'Youtube',
            ],
        ],
        'content'  => [],
        'desc'     => __( 'Show Lightbox', 'bdthemes-element-pack' ),
    ] );

    function ep_shortcode_lightbox($atts = null, $content = null) {

        $atts = shortcode_atts(
            array(
                'class' => '',
                'caption' => 'Youtube',
                'link' => 'https://youtu.be/aqz-KE-bpKQ'
            ),
            $atts, 'lightbox'
        );

        $output = '<span class="epsc-lightbox' . Element_Pack_Shortcodes::ep_get_css_class( $atts ) . '" bdt-lightbox>';
        $output .= '<a href="' . $atts['link'] . '" data-caption="' . $atts['caption'] . '" >';
        $output .= do_shortcode( $content );
        $output .= '</a>';
        $output .= '</span>';

        return $output;

    }

?>
