<?php
namespace Perfmatters;

class Utilities
{
    //get given post meta option for current post
    public static function get_post_meta($option) {

        global $post;

        if(!is_object($post)) {
            return false;
        }

        if(is_home()) {
            $post_id = get_queried_object_id();
        }

        if(is_singular() && isset($post)) {
            $post_id = $post->ID;
        }

        return (isset($post_id)) ? get_post_meta($post_id, $option, true) : false;
    }
}