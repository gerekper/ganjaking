<?php
namespace Perfmatters;

class Utilities
{
    //get given post meta option for current post
    public static function get_post_meta($option) {

        global $post;

        //print_r($post);

        if(!is_object($post)) {
            //echo 'false';
            return false;
        }

        //echo 'proceeed';

        if(is_home()) {
            $post_id = get_queried_object_id();
        }

        if(is_singular() && isset($post)) {
            $post_id = $post->ID;
        }

        return (isset($post_id)) ? get_post_meta($post_id, $option, true) : false;
    }

    //remove unecessary bits from html for search
    public static function clean_html($html) {

        //remove existing script tags
        $html = preg_replace('/<script\b(?:[^>]*)>(?:.+)?<\/script>/Umsi', '', $html);

        //remove existing noscript tags
        $html = preg_replace('#<noscript>(?:.+)</noscript>#Umsi', '', $html);

        return $html;
    }

    //get array of element attributes from attribute string
    public static function get_atts_array($atts_string) {
    
        if(!empty($atts_string)) {
            $atts_array = array_map(
                function(array $attribute) {
                    return $attribute['value'];
                },
                wp_kses_hair($atts_string, wp_allowed_protocols())
            );

            return $atts_array;
        }

        return false;
    }

    //get attribute string from array of element attributes
    public static function get_atts_string($atts_array) {

        if(!empty($atts_array)) {
            $assigned_atts_array = array_map(
            function($name, $value) {
                    if($value === '') {
                        return $name;
                    }
                    return sprintf('%s="%s"', $name, esc_attr($value));
                },
                array_keys($atts_array),
                $atts_array
            );
            $atts_string = implode(' ', $assigned_atts_array);

            return $atts_string;
        }

        return false;
    }
}