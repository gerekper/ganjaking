<?php
    if (!defined('ABSPATH')) exit; // Exit if accessed directly
    ep_add_shortcode([
        'id'             => 'countdown',
        'callback'       => 'ep_shortcode_countdown',
        'name'           => __('Countdown', 'bdthemes-element-pack'),
        'type'           => 'single',
        'atts'           => [
            'class' => [
                'type'    => 'extra_css_class',
                'name'    => __('Extra CSS class', 'bdthemes-element-pack'),
                'desc'    => __('Additional CSS class name(s) separated by space(s)', 'bdthemes-element-pack'),
                'default' => '',
            ]

        ],
        'desc'     => __('Show Countdown', 'bdthemes-element-pack'),
    ]);

    function ep_shortcode_countdown($atts = null) {

        $atts = shortcode_atts(array('class' => '', 'date' => '2020-12-12 12:00'), $atts, 'countdown');

        $with_gmt_time = date( 'Y-m-d H:i', strtotime( $atts['date'] ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
		$datetime      = new \DateTime($with_gmt_time);
		$final_time    = $datetime->format('c');

        $output = '<span class="epsc-countdown ' . Element_Pack_Shortcodes::ep_get_css_class($atts) . '" bdt-countdown="date: '. $final_time .'">';
        $output .= '<span class="bdt-countdown-days" bdt-tooltip="Days"></span>
        <span>:</span>
        <span class="bdt-countdown-hours" bdt-tooltip="Hours"></span>
        <span>:</span>
        <span class="bdt-countdown-minutes" bdt-tooltip="Minutes"></span>
        <span>:</span>
        <span class="bdt-countdown-seconds" bdt-tooltip="Seconds"></span>';
        $output .= '</span>';

        return $output;

    }
?>
