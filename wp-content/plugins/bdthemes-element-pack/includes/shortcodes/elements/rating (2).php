<?php
    if (!defined('ABSPATH')) exit; // Exit if accessed directly
    ep_add_shortcode([
        'id'             => 'rating',
        'callback'       => 'ep_shortcode_rating',
        'name'           => __('Rating', 'bdthemes-element-pack'),
        'type'           => 'single',
        'atts'           => [
            'class' => [
                'type'    => 'extra_css_class',
                'name'    => __('Extra CSS class', 'bdthemes-element-pack'),
                'desc'    => __('Additional CSS class name(s) separated by space(s)', 'bdthemes-element-pack'),
                'default' => '',
            ],
            'number' => [
                'type'    => 'text',
                'default' => 5,
            ],
        ],
        'desc'           => __('Show Rating', 'bdthemes-element-pack'),
    ]);

    function ep_shortcode_rating($atts = null) {

        $atts = shortcode_atts(array('class' => '', 'score' => 5), $atts, 'rating');

        if (preg_match('/\./', $atts['score'])) {
            $ratingValue = explode(".",$atts['score']);
            $firstVal    = ( $ratingValue[0] <= 5 ) ? $ratingValue[0] : 5;
            $secondVal   = ( $ratingValue[1] < 5 ) ? 0 : 5;
        } else {
            $firstVal    = ( $atts['score'] <= 5 ) ? $atts['score'] : 5;
            $secondVal   = 0;
        }

        $score       = $firstVal . '-' . $secondVal;



        $output = '<span class="epsc-rating epsc-rating-'.$score.' ' . Element_Pack_Shortcodes::ep_get_css_class($atts) . '">';
        $output .= '<span class="epsc-rating-item"><i class="ep-icon-star" aria-hidden="true"></i></span>
            <span class="epsc-rating-item"><i class="ep-icon-star" aria-hidden="true"></i></span>
            <span class="epsc-rating-item"><i class="ep-icon-star" aria-hidden="true"></i></span>
            <span class="epsc-rating-item"><i class="ep-icon-star" aria-hidden="true"></i></span>
            <span class="epsc-rating-item"><i class="ep-icon-star" aria-hidden="true"></i></span>';
        $output .= '</span>';

        wp_enqueue_style('ep-font');
        return $output;

    }
?>
