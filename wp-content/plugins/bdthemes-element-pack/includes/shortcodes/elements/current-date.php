<?php
    if (!defined('ABSPATH')) exit; // Exit if accessed directly
    ep_add_shortcode([
        'id'       => 'current_date',
        'callback' => 'ep_shortcode_current_date',
        'name'     => __('Current Date', 'bdthemes-element-pack'),
        'type'     => 'single',
        'atts'     => [
            'class' => [
                'type'    => 'extra_css_class',
                'name'    => __('Extra CSS class', 'bdthemes-element-pack'),
                'desc'    => __('Additional CSS class name(s) separated by space(s)', 'bdthemes-element-pack'),
                'default' => '',
            ],
        ],
        'desc'     => __('Show Current Date', 'bdthemes-element-pack'),
    ]);

    function ep_shortcode_current_date($atts = null) {

        $atts = shortcode_atts(array(
            'class'        => '',
            'day_offset'   => '',
            'month_offset' => '',
            'year_offset'  => '',
            'format'       => get_option('date_format')
        ) , $atts, 'current-date');

        $output       = '<span class="epsc-current-date' . Element_Pack_Shortcodes::ep_get_css_class($atts) . '">';

        $year_offset  = ($atts['year_offset']) ? $atts['year_offset'] : '0';
        $month_offset = ($atts['month_offset']) ? $atts['month_offset'] : '0';
        $day_offset   = ($atts['day_offset']) ? $atts['day_offset'] : '0';

        $date_offset  = mktime(0, 0, 0, date("m")+$month_offset  , date("d")+$day_offset, date("Y")+$year_offset);


        if ( $year_offset or $month_offset or $day_offset ) {
            $output      .= date($atts['format'], $date_offset );
        } else {
            $output      .= date($atts['format']);
        }


        $output      .= '</span>';

        return $output;
    }
