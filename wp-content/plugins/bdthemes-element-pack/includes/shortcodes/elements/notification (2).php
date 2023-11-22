<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
    ep_add_shortcode([
        'id'             => 'notification',
        'callback'       => 'ep_shortcode_notification',
        'name'           => __('Notification', 'bdthemes-element-pack'),
        'type'           => 'wrap',
        'atts'           => [
            'class' => [
                'type'    => 'extra_css_class',
                'name'    => __('Extra CSS class', 'bdthemes-element-pack'),
                'desc'    => __('Additional CSS class name(s) separated by space(s)', 'bdthemes-element-pack'),
                'default' => '',
            ],
            'message' => [
                'type' => 'text',
                'default' => 'Notification message',
            ]
        ],
        'content'  => [],
        'desc'     => __('Show Notification', 'bdthemes-element-pack'),
    ]);

    function ep_shortcode_notification($atts = null, $content = null) {

        $atts = shortcode_atts(array('class' => '', 'message' => 'Notification message'), $atts, 'notification');

        $output = '<span class="epsc-notification ' . Element_Pack_Shortcodes::ep_get_css_class($atts) . '" type="button" onclick="bdtUIkit.notification({message: \''.$atts['message'].'\'})">';
        $output .= do_shortcode($content);
        $output .= '</span>';

        return $output;


    }
?>
