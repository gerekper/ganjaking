<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
    ep_add_shortcode([
        'id'             => 'current_user',
        'callback'       => 'ep_shortcode_current_user',
        'name'           => __('Current User', 'bdthemes-element-pack'),
        'type'           => 'single',
        'atts'           => [
            'class' => [
                'type'    => 'extra_css_class',
                'name'    => __('Extra CSS class', 'bdthemes-element-pack'),
                'desc'    => __('Additional CSS class name(s) separated by space(s)', 'bdthemes-element-pack'),
                'default' => '',
            ],
        ],
        'desc'           => __('Show Current User', 'bdthemes-element-pack'),
    ]);

    function ep_shortcode_current_user($atts = null) {

        $atts = shortcode_atts(array('class' => ''), $atts, 'current-user');
        $current_user = wp_get_current_user();

        $output = '<span class="epsc-current-user' . Element_Pack_Shortcodes::ep_get_css_class($atts) . '">';
        $output .= $current_user->user_login;
        $output .= '</span>';

        return $output;

    }
?>
