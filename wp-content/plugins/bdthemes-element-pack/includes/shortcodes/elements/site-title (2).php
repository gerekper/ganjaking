<?php
    if (!defined('ABSPATH')) exit; // Exit if accessed directly
    ep_add_shortcode([
        'id'             => 'site_title',
        'callback'       => 'ep_shortcode_site_title',
        'name'           => __('Site Title', 'bdthemes-element-pack'),
        'type'           => 'single',
        'atts'           => [
            'class' => [
                'type'    => 'extra_css_class',
                'name'    => __('Extra CSS class', 'bdthemes-element-pack'),
                'desc'    => __('Additional CSS class name(s) separated by space(s)', 'bdthemes-element-pack'),
                'default' => '',
            ],
        ],
        'desc'           => __('Show Site Title', 'bdthemes-element-pack'),
    ]);

    function ep_shortcode_site_title($atts = null) {

        $atts = shortcode_atts(array('class' => ''), $atts, 'site-title');

        $output = '<span class="epsc-site-title' . Element_Pack_Shortcodes::ep_get_css_class($atts) . '">';
        $output .= get_bloginfo( 'name' );
        $output .= '</span>';

        return $output;

    }
?>
