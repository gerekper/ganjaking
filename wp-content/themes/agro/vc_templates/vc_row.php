<?php
if (! defined('ABSPATH')) {
    die('-1');
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $el_class
 * @var $full_width
 * @var $full_height
 * @var $equal_height
 * @var $columns_placement
 * @var $content_placement
 * @var $parallax
 * @var $parallax_image
 * @var $css
 * @var $el_id
 * @var $video_bg
 * @var $video_bg_url
 * @var $video_bg_parallax
 * @var $parallax_speed_bg
 * @var $parallax_speed_video
 * @var $content - shortcode content
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Row
*/

$el_class = $full_height = $parallax_speed_bg = $parallax_speed_video = $full_width = $equal_height = $flex_row =
$columns_placement = $content_placement = $parallax = $parallax_image = $css = $el_id = $video_bg = $video_bg_url = $video_bg_parallax  =  '';

//extra row atts
$agro_row_prepad = '';

$agro_parallax_speed = $agro_bg_pos = $agro_row_overflow = $agro_parallax_bg_opacity = $agro_mobile_parallax = '';
$agro_lg_bgpos = $agro_md_bgpos = $agro_sm_bgpos = $agro_xs_bgpos = '';

$agro_lg_custom_bgpos = $agro_md_custom_bgpos = $agro_sm_custom_bgpos = $agro_xs_custom_bgpos = '';

$agro_bgsize = $agro_custom_bgsize = $agro_custom_bgpos = $agro_bgrepeat = $agro_bg_attachment = '';

$agro_row_overlayclr = $agro_row_overlay_type = $agro_bg_zindex = '';

$agro_md_hidebg = $agro_sm_hidebg = $agro_xs_hidebg = '';

$agro_md_css = $agro_sm_css = $agro_xs_css = '';
//extra row atts

$disable_element 	= '';
$output 			= $after_output = '';
$atts 				= vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);

wp_enqueue_script('wpb_composer_front_js');


$el_class = $this->getExtraClass($el_class);



if ('container' === $full_width || 'container-fluid' === $full_width || 'container-stretch' === $full_width || 'container-null' === $full_width) {
    $css_classes = array(
        'agro-section-wrapper',
        $agro_row_prepad,
        $el_class,
        vc_shortcode_custom_css_class($css),
    );

    //bg custom class
    $css_classes[] = $agro_row_overflow != '' ? 'overflow_'.$agro_row_overflow : '';
    $css_classes[] = $agro_bg_zindex != '' ? 'zindex'.$agro_bg_zindex : '';
    $css_classes[] = $agro_parallax_bg_opacity != '' ? 'parallax-bg-opacity-'.$agro_parallax_bg_opacity : '';
    $css_classes[] = $agro_md_hidebg != '' ? 'row-bg-image-hide-992' : '';
    $css_classes[] = $agro_sm_hidebg != '' ? 'row-bg-image-hide-768' : '';
    $css_classes[] = $agro_xs_hidebg != '' ? 'row-bg-image-hide-576' : '';

    if ('yes' === $disable_element) {
        if (vc_is_page_editable()) {
            $css_classes[] = 'vc_hidden-lg vc_hidden-xs vc_hidden-sm vc_hidden-md';
        } else {
            return '';
        }
    }

    if (vc_shortcode_custom_css_has_property($css, array(
            'border',
            'background',
        )) || $video_bg || $parallax
    ) {
        $css_classes[] = 'nt-has-fill';
    }

    // full-height wrapper
    if (! empty($full_height)) {
        $css_classes[] = 'vc_row-o-full-height';
        if (! empty($columns_placement)) {
            $flex_row = true;
            $css_classes[] = 'columns-' . $columns_placement;
            if ('stretch' === $columns_placement) {
                $css_classes[] = 'vc_row-o-equal-height';
            }
        }
    }

    if (! empty($equal_height)) {
        $flex_row = true;
        $css_classes[] = 'vc_row-o-equal-height';
    }

    if (! empty($content_placement)) {
        $flex_row = true;
        $css_classes[] = 'content-' . $content_placement;
    }

    if (! empty($flex_row)) {
        $css_classes[] = 'content-flex';
    }

    if (! empty($atts['gap'])) {
        $css_classes[] = 'vc_column-gap-' . $atts['gap'];
    }

    if (! empty($atts['rtl_reverse'])) {
        $css_classes[] = 'vc_rtl-columns-reverse';
    }

    $wrapper_attributes = array();
    // build attributes for wrapper
    if (! empty($el_id)) {
        $wrapper_attributes[] = 'id="' . esc_attr($el_id) . '"';
    }

    //CUSTOM PARALLAX CODE START
    if (! empty($video_bg) && ! empty($video_bg_url)) {
        $parallax = $video_bg_parallax;
    }
    if (! empty($parallax)) {
        wp_enqueue_script('jarallax');

        //CUSTOM PARALLAX CODE START
        $agro_bgpos = $agro_bg_pos == 'custom' ? $agro_custom_bgpos : $agro_bg_pos;
        $agro_bgsize = $agro_bgsize == 'custom' ? $agro_custom_bgsize : $agro_bgsize;

        $wrapper_attributes[] = 'data-jarallax';
        $wrapper_attributes[] = $agro_parallax_speed ? 'data-speed="'.esc_attr($agro_parallax_speed).'"' : 'data-speed="0.2"';
        $wrapper_attributes[] = $agro_bgpos ? 'data-img-position="'.esc_attr($agro_bgpos).'"' : 'data-img-position="center"';
        $wrapper_attributes[] = $agro_bgsize ? 'data-img-size="'.esc_attr($agro_bgsize).'"' : 'data-img-size="cover"';
        $wrapper_attributes[] = $agro_bgrepeat ? 'data-img-repeat="'.esc_attr($agro_bgrepeat).'"' : 'data-img-repeat="no-repeat"';
        $wrapper_attributes[] = $agro_mobile_parallax ? 'data-mobile-parallax="'.esc_attr($agro_mobile_parallax).'"' : '';

        $css_classes[] = 'jarallax jarallax-bg jarallax-' . $parallax;

        if ('agro-scroll' ===  $parallax) {
            $wrapper_attributes[] = 'data-type="scroll"';
            $css_classes[] = 'jarallax-bg-scroll mobile-parallax-'.$agro_mobile_parallax .'';
        } elseif ('agro-scale' ===  $parallax) {
            $wrapper_attributes[] = 'data-type="scale"';
            $css_classes[] = 'jarallax-bg-scale mobile-parallax-'.$agro_mobile_parallax .'';
        } elseif ('agro-opacity' ===  $parallax) {
            $wrapper_attributes[] = 'data-type="opacity"';
            $css_classes[] = 'jarallax-bg-opacity mobile-parallax-'.$agro_mobile_parallax .'';
        } elseif ('agro-scroll-opacity' ===  $parallax) {
            $wrapper_attributes[] = 'data-type="scroll-opacity"';
            $css_classes[] = 'jarallax-bg-scroll-opacity mobile-parallax-'.$agro_mobile_parallax .'';
        } elseif ('agro-scale-opacity' ===  $parallax) {
            $wrapper_attributes[] = 'data-type="scale-opacity"';
            $css_classes[] = 'jarallax-bg-scale-opacity mobile-parallax-'.$agro_mobile_parallax .'';
        } else {
            $wrapper_attributes[] = 'data-type="scroll"';
            $css_classes[] = 'jarallax-bg-scroll mobile-parallax-'.$agro_mobile_parallax .'';
        }


        if (! empty($video_bg) && ! empty($video_bg_url)) {
            $wrapper_attributes[] = 'data-jarallax-video="'.esc_url($video_bg_url).'"';
        }

        if (! empty($parallax_image)) {
            $parallax_image_id = preg_replace('/[^\d]/', '', $parallax_image);
            $parallax_image_src = wp_get_attachment_image_src($parallax_image_id, 'full');
            if (! empty($parallax_image_src[0])) {
                $parallax_image_src = $parallax_image_src[0];
            }

            $wrapper_attributes[] = 'data-img-src="' . esc_attr($parallax_image_src) . '"';
        }
    }

    //craete uniq class for row data css
    $unique_class = 'nt_section_1541'.mt_rand(5, 10000);
    //add extra css to custom agro_vc_extra_css function
    $agro_row_extra = array();
    $agro_row_extra[] = $agro_row_overflow == 'visible' ? '.'.$unique_class.'{overflow:'.$agro_row_overflow.'!important;}' : '';
    $agro_row_extra[] = $agro_row_overflow == 'visible' && $agro_bg_zindex != '' ? '.'.$unique_class.'{z-index:'.$agro_bg_zindex.'!important;}' : '';
    $agro_row_extra[] = $agro_bg_attachment != '' ? '.'.$unique_class.'{background-attachment:'.$agro_bg_attachment.'!important;}' : '';
    //add to custom css function
    $agro_row_data = (class_exists('Vc_Manager')) ? agro_vc_extra_css($atts, $unique_class, $agro_row_extra) : '';
    $css_classes[]= $agro_row_data != '' ? $unique_class : '';
    $css_classes[] = $agro_row_data != '' ? 'nt-has-responsive-data' : '';
    $overlay_section = $agro_row_overlayclr != '' ? '<div class="has-overlay" style="background:'.esc_attr($agro_row_overlayclr).'"></div>' : '';

    $css_class = preg_replace('/\s+/', ' ', apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, implode(' ', array_filter(array_unique($css_classes))), $this->settings['base'], $atts));
    $wrapper_attributes[] = 'class="' . esc_attr(trim($css_class)) . '"';

    if ('container' === $full_width || 'container-fluid' === $full_width) {
        $output .= '<div class="'.$full_width.'">';
        $output .= $overlay_section;
        $output .= '<div ' . implode(' ', $wrapper_attributes) .$agro_row_data.'>';
        $output .= '<div class="row">';
    }
    if ('container-stretch' === $full_width) {
        $output .= '<div ' . implode(' ', $wrapper_attributes) .$agro_row_data.'>';
        $output .= $overlay_section;
        $output .= '<div class="container '.$full_width.'">';
        $output .= '<div class="row">';
    }
    if ('container-null' === $full_width) {
        $output .= '<div ' . implode(' ', $wrapper_attributes) .$agro_row_data.'>';
        $output .= $overlay_section;
    }
    $output .= wpb_js_remove_wpautop($content);

    if ('container' === $full_width || 'container-fluid' === $full_width || 'container-stretch' === $full_width) {
        $output .= '</div>';// end row
    $output .= '</div>';// end container
    }
    $output .= '</div>';// end wrapper
    if (! empty($full_width)) {
        $output .= '<div class="nt_clearfix"></div>';
    }

    /*
    * Custom section end
    *
    * block below is default vc_row template
    *
    *
    */
} else {
    $css_classes = array(
        'vc_row',
        'wpb_row',
        //deprecated
        'vc_row-fluid',
        $el_class,
        vc_shortcode_custom_css_class($css),
    );

    if ('yes' === $disable_element) {
        if (vc_is_page_editable()) {
            $css_classes[] = 'vc_hidden-lg vc_hidden-xs vc_hidden-sm vc_hidden-md';
        } else {
            return '';
        }
    }

    if (vc_shortcode_custom_css_has_property($css, array(
            'border',
            'background',
        )) || $video_bg || $parallax
    ) {
        $css_classes[] = 'vc_row-has-fill';
    }

    if (! empty($atts['gap'])) {
        $css_classes[] = 'vc_column-gap-' . $atts['gap'];
    }

    if (! empty($atts['rtl_reverse'])) {
        $css_classes[] = 'vc_rtl-columns-reverse';
    }

    $wrapper_attributes = array();
    // build attributes for wrapper
    if (! empty($el_id)) {
        $wrapper_attributes[] = 'id="' . esc_attr($el_id) . '"';
    }
    if (! empty($full_width)) {
        $wrapper_attributes[] = 'data-vc-full-width="true"';
        $wrapper_attributes[] = 'data-vc-full-width-init="false"';
        if ('stretch_row_content' === $full_width) {
            $wrapper_attributes[] = 'data-vc-stretch-content="true"';
        } elseif ('stretch_row_content_no_spaces' === $full_width) {
            $wrapper_attributes[] = 'data-vc-stretch-content="true"';
            $css_classes[] = 'vc_row-no-padding';
        }
        $after_output .= '<div class="vc_row-full-width vc_clearfix"></div>';
    }

    if (! empty($full_height)) {
        $css_classes[] = 'vc_row-o-full-height';
        if (! empty($columns_placement)) {
            $flex_row = true;
            $css_classes[] = 'vc_row-o-columns-' . $columns_placement;
            if ('stretch' === $columns_placement) {
                $css_classes[] = 'vc_row-o-equal-height';
            }
        }
    }

    if (! empty($equal_height)) {
        $flex_row = true;
        $css_classes[] = 'vc_row-o-equal-height';
    }

    if (! empty($content_placement)) {
        $flex_row = true;
        $css_classes[] = 'vc_row-o-content-' . $content_placement;
    }

    if (! empty($flex_row)) {
        $css_classes[] = 'vc_row-flex';
    }

    $has_video_bg = (! empty($video_bg) && ! empty($video_bg_url) && vc_extract_youtube_id($video_bg_url));

    $parallax_speed = $parallax_speed_bg;
    if ($has_video_bg) {
        $parallax = $video_bg_parallax;
        $parallax_speed = $parallax_speed_video;
        $parallax_image = $video_bg_url;
        $css_classes[] = 'vc_video-bg-container';
        wp_enqueue_script('vc_youtube_iframe_api_js');
    }

    if (! empty($parallax)) {
        wp_enqueue_script('vc_jquery_skrollr_js');
        $wrapper_attributes[] = 'data-vc-parallax="' . esc_attr($parallax_speed) . '"'; // parallax speed
        $css_classes[] = 'vc_general vc_parallax vc_parallax-' . $parallax;
        if (false !== strpos($parallax, 'fade')) {
            $css_classes[] = 'js-vc_parallax-o-fade';
            $wrapper_attributes[] = 'data-vc-parallax-o-fade="on"';
        } elseif (false !== strpos($parallax, 'fixed')) {
            $css_classes[] = 'js-vc_parallax-o-fixed';
        }
    }

    if (! empty($parallax_image)) {
        if ($has_video_bg) {
            $parallax_image_src = $parallax_image;
        } else {
            $parallax_image_id = preg_replace('/[^\d]/', '', $parallax_image);
            $parallax_image_src = wp_get_attachment_image_src($parallax_image_id, 'full');
            if (! empty($parallax_image_src[0])) {
                $parallax_image_src = $parallax_image_src[0];
            }
        }
        $wrapper_attributes[] = 'data-vc-parallax-image="' . esc_attr($parallax_image_src) . '"';
    }
    if (! $parallax && $has_video_bg) {
        $wrapper_attributes[] = 'data-vc-video-bg="' . esc_attr($video_bg_url) . '"';
    }
    $css_class = preg_replace('/\s+/', ' ', apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, implode(' ', array_filter(array_unique($css_classes))), $this->settings['base'], $atts));
    $wrapper_attributes[] = 'class="' . esc_attr(trim($css_class)) . '"';

    $output .= '<div ' . implode(' ', $wrapper_attributes) . '>';
    $output .= wpb_js_remove_wpautop($content);
    $output .= '</div>';
    $output .= $after_output;
}
echo agro_vc_sanitize_data($output);
