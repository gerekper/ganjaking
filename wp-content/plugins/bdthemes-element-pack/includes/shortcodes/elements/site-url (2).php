<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
    ep_add_shortcode([
        'id'             => 'site_url',
        'callback'       => 'ep_shortcode_site_url',
        'name'           => __('Site URL', 'bdthemes-element-pack'),
        'type'           => 'single',
        'atts'           => [],
        'desc'           => __('Show Site URL', 'bdthemes-element-pack'),
    ]);

    function ep_shortcode_site_url($atts = null) {

        $output = get_site_url();

        return $output;

    }
?>