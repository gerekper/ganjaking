<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
ep_add_shortcode([
    'id'             => 'clipboard',
    'callback'       => 'ep_shortcode_clipboard',
    'name'           => __('Clipboard', 'bdthemes-element-pack'),
    'type'           => 'wrap',
    'atts'           => [
        'class' => [
            'type'    => 'extra_css_class',
            'name'    => __('Extra CSS class', 'bdthemes-element-pack'),
            'desc'    => __('Additional CSS class name(s) separated by space(s)', 'bdthemes-element-pack'),
            'default' => '',
        ],
    ],
    'content'  => [],
    'desc'     => __('Show Clipboard', 'bdthemes-element-pack'),
]);

function ep_shortcode_clipboard($atts = null, $content = null) {

    $atts = shortcode_atts(array('class' => ''), $atts, 'clipboard');
    $output = '<span class="epsc-clipboard' . Element_Pack_Shortcodes::ep_get_css_class($atts) . '" bdt-tooltip="Click to Copy">';
    $output .= do_shortcode( $content );
    $output .= '</span>';

    wp_enqueue_script('clipboard');
    ?>

    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            var clipboard = new ClipboardJS('.epsc-clipboard', {
                target: function(e) {
                    return e;
                }
            });

            clipboard.on('success', function (event) {
                event.clearSelection();
                event.trigger.setAttribute("bdt-tooltip", "Copied!");
                event.clearSelection();
                setTimeout(function () {
                    event.trigger.setAttribute("bdt-tooltip", "Click to Copy");
                }, 3000);
            });

        });
    </script>

    <?php

    return $output;

}

?>