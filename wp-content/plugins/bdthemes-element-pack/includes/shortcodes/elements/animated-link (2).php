<?php
    if (!defined('ABSPATH')) exit; // Exit if accessed directly

    ep_add_shortcode( [
        'id'         => 'animated_link',
        'callback'   => 'ep_shortcode_animated_link',
        'name'       => __( 'Animated Link', 'bdthemes-element-pack' ),
        'type'       => 'wrap',
        'atts'       => [
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
                'default' => 'metis',
            ],
            'style' => [
                'type'    => 'text',
                'default' => '',
            ],
        ],
        'content'  => [],
        'desc'     => __( 'Show Animated Link', 'bdthemes-element-pack' ),
    ] );

    function ep_shortcode_animated_link($atts = null, $content = null) {

        $direction_suffix    = is_rtl() ? '.rtl' : '';

        wp_enqueue_style('animated-link', BDTEP_ASSETS_URL . 'css/ep-animated-link' . $direction_suffix . '.css');

        $atts = shortcode_atts( array('class' => '', 'link' => '#', 'type' => 'metis', 'style' => ''), $atts, 'animated_link' );

        $data_text = ( $atts['type'] == 'leda' ) ? ' data-text="' . $content . '"' : '';

        $output = '<a class="bdt-ep-animated-link ' . Element_Pack_Shortcodes::ep_get_css_class( $atts ) . ' bdt-ep-animated-link--' . $atts['type'] . '" href="' . $atts['link'] . '" style="' . $atts['style'] . '"'. $data_text.' >';

        if ( $atts['type'] == 'leda' or $atts['type'] == 'elara' or $atts['type'] == 'ersa' or $atts['type'] == 'eirene' or $atts['type'] == 'helike' or $atts['type'] == 'iocaste' or $atts['type'] == 'herse' ) {
            $output .= '<span>';
        }

        $output .= do_shortcode( $content );

        if ( $atts['type'] == 'leda' or $atts['type'] == 'elara' or $atts['type'] == 'ersa' or $atts['type'] == 'eirene' or $atts['type'] == 'helike' ) {
            $output .= '</span>';

        } elseif ($atts['type'] == 'iocaste') {
            $output .= '</span>';
            $output .= '<svg class="bdt-link__graphic bdt-link__graphic--slide" width="300%" height="100%" viewBox="0 0 1200 60" preserveAspectRatio="none">
            <path d="M0,56.5c0,0,298.666,0,399.333,0C448.336,56.5,513.994,46,597,46c77.327,0,135,10.5,200.999,10.5c95.996,0,402.001,0,402.001,0"></path>
        </svg>';

        } elseif ($atts['type'] == 'herse') {
            $output .= '</span>';
            $output .= '<svg class="bdt-link__graphic bdt-link__graphic--slide" width="300%" height="100%" viewBox="0 0 1200 60" preserveAspectRatio="none">
            <path d="M0,56.5c0,0,298.666,0,399.333,0C448.336,56.5,513.994,46,597,46c77.327,0,135,10.5,200.999,10.5c95.996,0,402.001,0,402.001,0"></path>
        </svg>';
            $output .= '</a>';
        } elseif ($atts['type'] == 'carme') {
            $output .= '</span>';
            $output .= '<svg class="bdt-link__graphic bdt-link__graphic--stroke bdt-link__graphic--scribble" width="100%" height="9" viewBox="0 0 101 9"><path d="M.426 1.973C4.144 1.567 17.77-.514 21.443 1.48 24.296 3.026 24.844 4.627 27.5 7c3.075 2.748 6.642-4.141 10.066-4.688 7.517-1.2 13.237 5.425 17.59 2.745C58.5 3 60.464-1.786 66 2c1.996 1.365 3.174 3.737 5.286 4.41 5.423 1.727 25.34-7.981 29.14-1.294" pathLength="1"/></svg>';
        }

        $output .= '</a>';

        return $output;

    }

?>