<?php
if( !defined( 'ABSPATH' ) )
    exit;

if( !class_exists( 'YITH_WC_Surveys_Shortcode' ) ){

    class YITH_WC_Surveys_Shortcode{

        public static function print_survey_shortcode( $atts, $content=null ){


            $default = array(
                'survey_id' => ''
            );

            $default = shortcode_atts( $default, $atts );
            extract( $default );

            $survey_id  =   intval( yith_wpml_get_translated_id( $survey_id, 'yith_wc_surveys' ) );
            $survey_visible_in = get_post_meta( $survey_id, '_yith_survey_visible_in', true );
           
            if( is_numeric( $survey_id ) && 'other_page' === $survey_visible_in ){
                $default['survey_id'] = $survey_id;

                ob_start();
                 wc_get_template( 'surveys/survey_other_page_form.php', $default, '', YITH_WC_SURVEYS_TEMPLATE_PATH );
                $template = ob_get_contents();
                ob_end_clean();

                return $template;
            }


        }
    }
}

add_shortcode( 'yith_wc_surveys', array( 'YITH_WC_Surveys_Shortcode', 'print_survey_shortcode' ) );