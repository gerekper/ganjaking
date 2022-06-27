<?php
namespace Perfmatters;

class Buffer
{
    //initialize buffer
    public static function init()
    {
        //inital checks
        if(is_admin() || perfmatters_is_dynamic_request() || perfmatters_is_page_builder() || isset($_GET['perfmatters'])) {
            return;
        }

        //buffer is allowed
        if(!apply_filters('perfmatters_allow_buffer', true)) {
            return;
        }

        //add buffer action
        add_action('template_redirect', array('Perfmatters\Buffer', 'start'), -9999);
    }

    //start buffer
    public static function start()
    {
        if(has_filter('perfmatters_output_buffer_template_redirect')) {

            //exclude certain requests
            if(is_embed() || is_feed() || is_preview() || is_customize_preview()) {
                return;
            }

            //don't buffer amp
            if(function_exists('is_amp_endpoint') && is_amp_endpoint()) {
                return;
            }

            ob_start(function($html) {

                //run buffer filters
                $html = (string) apply_filters('perfmatters_output_buffer_template_redirect', $html);

                //return processed html
                return $html;
            });
        }
    }
}