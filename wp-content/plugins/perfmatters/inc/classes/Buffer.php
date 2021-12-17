<?php
namespace Perfmatters;

class Buffer
{
    //initialize buffer
    public static function init()
    {
        //initialize classes that filter the buffer
        Fonts::init();
        CDN::init();
        Images::init();
        Preload::init();

        //add buffer actions
        add_action('init', array('Perfmatters\Buffer', 'start'), 0);
        add_action('template_redirect', array('Perfmatters\Buffer', 'start'));
    }

    //start buffer
    public static function start()
    {
        $current_filter = current_filter();

        if(!empty($current_filter) && has_filter('perfmatters_output_buffer_' . $current_filter)) {

            ob_start(function($html) use ($current_filter) {
                 //exclude certain requests
                if(is_admin() || perfmatters_is_dynamic_request() || perfmatters_is_page_builder() || is_embed() || is_feed() || is_preview() || is_customize_preview() || isset($_GET['perfmatters'])) {
                    return $html;
                }

                //run buffer filters
                $html = (string) apply_filters('perfmatters_output_buffer_' . $current_filter, $html);

                //return processed html
                return $html;
            });
        }
    }
}