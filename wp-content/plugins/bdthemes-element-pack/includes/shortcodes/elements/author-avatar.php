<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

    ep_add_shortcode([
        'id'             => 'author_avatar',
        'callback'       => 'ep_shortcode_author_avatar',
        'name'           => __('Author Avatar', 'bdthemes-element-pack'),
        'type'           => 'single',
        'atts'           => [
            'class' => [
                'type'    => 'extra_css_class',
                'name'    => __('Extra CSS class', 'bdthemes-element-pack'),
                'desc'    => __('Additional CSS class name(s) separated by space(s)', 'bdthemes-element-pack'),
                'default' => '',
            ],
        ],
        'desc'           => __('Show Author Avatar', 'bdthemes-element-pack'),
    ]);

    function ep_shortcode_author_avatar($atts = null) {

        $atts = shortcode_atts(array('class' => ''), $atts, 'author-avatar');

        $output = '<span class="epsc-author-avatar' . Element_Pack_Shortcodes::ep_get_css_class($atts) . '">';
        $output .= get_avatar(get_the_author_meta('ID'), 36);
        $output .= '</span>';

        return $output;

    }
?>
